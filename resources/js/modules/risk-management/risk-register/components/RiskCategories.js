import React, {useEffect, useState} from 'react';
import {useSelector} from "react-redux";
import RiskItemToggle from "./CategoryItemToggle";
import {Accordion} from "react-bootstrap";
import RiskItemsSection from "./RiskItemsSection";


const RiskCategories = (props) => {
    const {filters} = props;

    const [riskCategories, setRiskCategories] = useState([]);
    const [loading, setLoading] = useState(false);

    const appDataScope = useSelector(state => state.appDataScope.selectedDataScope.value);

    const fetchRiskCategories = async () => {
        setLoading(true);
        try {
            const {data} = await axiosFetch.get('risks/risks-register-react', {
                params: {
                    search: filters.search_term,
                    only_incomplete: filters.only_incomplete,
                    data_scope: appDataScope
                }
            });
            setRiskCategories(data.data);
            setLoading(false);
        }catch (err) {

        }
    }

    useEffect(() => {
        fetchRiskCategories();
    }, [filters, appDataScope]);

    const handleDeleteCategory = id => setRiskCategories(riskCategories.filter(r => r.id !== id));
    const handleUpdateCategoryRisksCount = (id, offset_total, offset_incomplete) => {
        setRiskCategories(riskCategories.map(c => c.id !== id ? c : ({...c, total_risks: c.total_risks-offset_total, total_incomplete_risks: c.total_incomplete_risks -offset_incomplete })));
    }

    return (
        <div>
            <Accordion>
                {riskCategories.map((category, index) => {
                    const eventkey = `category_${category.id}`;
                    return (
                        <div key={index}>
                            <RiskItemToggle eventKey={eventkey} category={category} />
                            <Accordion.Collapse eventKey={eventkey}>
                                <RiskItemsSection primaryFilters={filters} eventKey={eventkey} categoryId={category.id} onDeleteCategory={handleDeleteCategory} onUpdateCategoryRisksCount={handleUpdateCategoryRisksCount} />
                            </Accordion.Collapse>
                        </div>
                    )
                })}
                {!loading && riskCategories.length === 0 && (<p className="empty-data-section"> No records found</p>)}
            </Accordion>
        </div>
    );
};

export default RiskCategories;
