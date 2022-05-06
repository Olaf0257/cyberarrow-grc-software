import React, { useEffect, useState } from "react";

import AppLayout from "../../../layouts/app-layout/AppLayout";

import RiskRegisterFilters from "./components/RiskRegisterFilters";
import RiskRegisterActions from "./components/RiskRegisterActions";
import RiskCategories from "./components/RiskCategories";

import "./styles/style.scss";
import "react-toastify/dist/ReactToastify.css";
import { ToastContainer } from "react-toastify";
import FlashMessages from "../../../common/FlashMessages";
import Breadcrumb from "../../../common/breadcumb/Breadcumb";

const RiskRegister = () => {
    const [searchTerm, setSearchTerm] = useState("");
    const [filters, setFilters] = useState({
        search_term: "",
        only_incomplete: false,
    });
    
    const breadcumbsData = {
        "title": "View Risk",
        "breadcumbs": [
            {
                "title": "Risk Management",
                "href": route('risks.dashboard.index')
            },
            {
                "title": "Risk Register",
                "href": "#"
            }
        ]
    }
    const handleCheck = () =>
        setFilters({ ...filters, only_incomplete: !filters.only_incomplete });
    const handleTermChange = (e) => setSearchTerm(e.target.value);

    useEffect(() => {
        document.title = "Risk Register";
        if(searchTerm.length >= 3) return setFilters({...filters, search_term: searchTerm});
        if(filters.search_term !== '' && searchTerm.length <3) setFilters({...filters, search_term: ''});
    }, [searchTerm]);

    return (
        <AppLayout>
            <div id="risk-register-page">
                <Breadcrumb data={breadcumbsData}></Breadcrumb>
                <FlashMessages />
                <div className="row">
                    <div className="col-xl-12">
                        <div className="card">
                            <div className="card-body project-box">
                                <RiskRegisterActions />
                                <RiskRegisterFilters
                                    searchTerm={searchTerm}
                                    onTermChange={handleTermChange}
                                    onlyIncomplete={filters.only_incomplete}
                                    onCheck={handleCheck}
                                />

                                <div id="risk-by-category-section">
                                    <RiskCategories filters={filters} />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
};

export default RiskRegister;
