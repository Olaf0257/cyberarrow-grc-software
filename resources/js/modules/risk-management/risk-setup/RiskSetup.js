import React,{Fragment,useEffect} from 'react';
import './styles/RiskSetup.scss';
import BreadcumbsComponent from '../../../common/breadcumb/Breadcumb';
import ProjectBox from './components/ProjectBox';
import AppLayout from '../../../layouts/app-layout/AppLayout';

function RiskSetup(props) {

    useEffect(() => {
        document.title = "Risk Setup";
    }, []);

    const breadcumbsData = {
        title: "Risk Setup",
        breadcumbs: [
            {
                title: "Risk Management",
                href: `${appBaseURL}/risks/dashboard`,
            },
            {
                title: "Risk Setup",
                href: "",
            },
        ],
    };

    const projects = [
        {
            title: "Manual Import",
            description:
                "Manual import allows you to manually upload  a large number of risks using a CSV template. This  is great for organizations who want to bulk upload risks.",
            href: route("risks.manual.setup"),
        },
        {
            title: "Wizard Import",
            description:
                "The wizard allows you to automatically generate risks based on compliance projects and to choose risks based on international standards. ",
            href: route("risks.wizard.setup"),
        },
    ];

    return (
        <AppLayout>
            <Fragment>
                <BreadcumbsComponent data={breadcumbsData} />
                <ProjectBox projectsData={projects} />
            </Fragment>
        </AppLayout>
    );
}

export default RiskSetup;
