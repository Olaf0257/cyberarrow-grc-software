import React, {useContext, useEffect, useState} from 'react';

import {AccordionContext} from "react-bootstrap";
import Swal from 'sweetalert2';


import RiskItemsTable from "./RiskItemsTable";
import {useSelector} from "react-redux";

const RiskItemsSection = props => {
    const {primaryFilters, categoryId, eventKey, onDeleteCategory, onUpdateCategoryRisksCount} = props;

    const [risks, setRisks] = useState([]);
    const [pagination, setPagination] = useState({
        current_page: 1,
        total: 1
    });

    const appDataScope = useSelector(state => state.appDataScope.selectedDataScope.value);

    const [q, setQ] = useState('');
    const [searchTerm, setSearchTerm] = useState('');

    const currentEventKey = useContext(AccordionContext);

    const fetchRisks = async (page = 1) => {
        try {
            const {data} = await axiosFetch.get(`risks/risks-register-react/${categoryId}/registered-risks`, {
                params: {
                    search: primaryFilters.search_term,
                    only_incomplete: primaryFilters.only_incomplete,
                    search_within_category: q,
                    data_scope: appDataScope,
                    page
                }
            });

            if(data.data.length === 0 && q === '') return onDeleteCategory(categoryId);
            // if no search results, keep the previous ones
            if(data.data.length === 0 && risks.length !== 0 && q !== '') return;
            setRisks(data.data);
            setPagination({
                ...pagination,
                current_page: data.current_page,
                total: data.last_page
            });
        } catch (err) {

        }
    };

    useEffect(() => {
        if (searchTerm.length >= 3) return setQ(searchTerm);
        if (searchTerm.length < 3 && q !== '') setQ('');
    }, [searchTerm]);

    useEffect(() => {
        if (currentEventKey.activeEventKey === eventKey)
            fetchRisks();
    }, [primaryFilters, currentEventKey, q, appDataScope]);

    const handleOnPaginate = (page) => fetchRisks(page);
    const handleUpdateRiskStatus = (riskId, is_complete) => setRisks(risks.map(r => r.id !== riskId ? r : ({...r, is_complete})));
    const removeRiskFromTable = (riskId) => {
        const offset_incomplete = risks.find(r => r.id === riskId).is_complete ? 0 : 1;
        if (risks.length - 1 <= 0) {
            if (pagination.current_page === 1){
                if(q !== ''){
                    // there might be some risks left that don't
                    // match the query
                    setSearchTerm('');
                }else{
                    return onDeleteCategory(categoryId);
                }
            }else{
                fetchRisks(pagination.current_page - 1);
            }
        } else {
            if (pagination.current_page === pagination.total) {
                // just remove the risk
                // because we're in the last page
                setRisks(risks.filter(r => r.id !== riskId));
            } else {
                //re-fetch the current page
                fetchRisks(pagination.current_page);
            }
        }
        onUpdateCategoryRisksCount(categoryId, 1, offset_incomplete);
    }
    const handleOnDelete = riskId => {
        Swal.fire({
            title: "Are you sure that you want to delete the risk?",
            text: "This action is irreversible and any mapped controls will be unmapped.",
            showCancelButton: true,
            confirmButtonColor: '#f1556c',
            confirmButtonText: 'Yes, delete it!',
            icon: 'warning',
            iconColor: '#f1556c'

        }).then((result) => {
            if (result.isConfirmed) {
                axiosFetch.get(`risks/risks-register/${riskId}/delete`)
                    .then(res => {
                        if (res.data.status === 200) {
                            Swal.fire({
                                text: res.data.message,
                                confirmButtonColor: '#b2dd4c',
                                icon: 'success',
                            });
                            removeRiskFromTable(riskId);
                        }
                    });
            }
        })
    }

    return (
        <div>
            <div className="top__text d-flex p-2">
                <h5>Search Risk Items</h5>
                <div className="searchbox animated zoomIn ms-auto">
                    <input type="text" placeholder="Search by Risk Name" value={searchTerm}
                           onChange={e => setSearchTerm(e.target.value)}
                           className="search"/>
                    <i className="fas fa-search"/>
                </div>
            </div>
            <RiskItemsTable risks={risks} primaryFilters={primaryFilters} onDelete={handleOnDelete} removeRiskFromTable={removeRiskFromTable} onPaginate={handleOnPaginate}
                            pagination={pagination} handleUpdateRiskStatus={handleUpdateRiskStatus} onUpdateCategoryRisksCount={onUpdateCategoryRisksCount} />
        </div>
    );
};

export default RiskItemsSection;
