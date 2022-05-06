import React, {useEffect, useState} from 'react';

import AppLayout from "../../../layouts/app-layout/AppLayout";
import BreadcumbComponent from "../../../common/breadcumb/Breadcumb";
import ContentLoader from "../../../common/content-loader/ContentLoader";

import ProjectItem from "./components/ProjectItem";
import AddProjectCard from "./components/AddProjectCard";

import "react-datetime/css/react-datetime.css";
import './styles/style.css';

import {useSelector} from "react-redux";
import DuplicateProjectModal from "./components/DuplicateProjectModal";

const Index = () => {
    const [projects, setProjects] = useState([]);
    const [loading, setLoading] = useState(true);
    const [filter, setFilter] = useState('active');
    const [refreshToggle, setRefreshToggle] = useState(false);
    const [q, setQ] = useState('');

    const [selectedProject, setSelectedProject] = useState(null);
    const [showDuplicateModal, setShowDuplicateModal] = useState(false);

    const appDataScope = useSelector(state => state.appDataScope.selectedDataScope.value);

    const reload = (redirect = false) => {
        if (filter === 'archived' && redirect) return setFilter('active');
        if (q !== '') return setQ('');
        setRefreshToggle(!refreshToggle);
    }

    const fetchProjects = () => {
        setLoading(true);
        axiosFetch.get(route('third-party-risk.projects.get-json-data'), {
            params: {
                search: q,
                filter
            }
        })
            .then(({data}) => {
                setProjects(data.projects);
                setLoading(false);
            })
    }

    useEffect(() => {
        document.title = "Third Party Risk Projects";
    }, []);

    useEffect(() => {
        fetchProjects();
    }, [q, filter, refreshToggle, appDataScope])

    const breadcrumbs = {
        title: 'View Projects',
        breadcumbs: [
            {
                title: 'Third Party Risk',
                href: ''
            },
            {
                title: 'Projects',
                href: ''
            }
        ]
    };

    const handleDuplicate = project => {
        setSelectedProject(project);
        setShowDuplicateModal(true);
    }

    return (
        <AppLayout>
            <BreadcumbComponent data={breadcrumbs}/>
            <div className="row mb-3">
                <div className="col-md-8">
                    <button
                        type="button"
                        className={`btn btn-primary mb-3 mb-md-0 me-1 ${filter === 'active' ? 'active' : ''}`}
                        onClick={() => setFilter('active')}
                    >
                        Active Projects
                    </button>
                    <button
                        type="button"
                        className={`btn btn-primary mb-3 mb-md-0 ${filter === 'archived' ? 'active' : ''}`}
                        onClick={() => setFilter('archived')}
                    >
                        Archived Projects
                    </button>
                </div>
                <div className="col-md-4 clearfix">
                    <div className="float-end">
                        <div className="ms-md-3 mb-3">
                            <div className="row align-items-center">
                                <div className="col-12">
                                    <input
                                        type="text"
                                        value={q}
                                        onChange={e => setQ(e.target.value)}
                                        className="form-control form-control-sm"
                                        placeholder="Search..."
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <DuplicateProjectModal
                show={showDuplicateModal}
                reload={reload}
                selectedProject={selectedProject}
                handleClose={() => setShowDuplicateModal(false)}
            />
            <ContentLoader
                show={loading}
            >
                <div className="row">
                    <AddProjectCard reload={reload} hidden={filter === 'archived'}/>
                    {projects.map(project => (
                        <ProjectItem
                            reload={reload}
                            project={project}
                            key={project.id}
                            handleDuplicate={handleDuplicate}
                        />
                    ))}
                </div>
            </ContentLoader>
        </AppLayout>
    )
};

export default Index;
