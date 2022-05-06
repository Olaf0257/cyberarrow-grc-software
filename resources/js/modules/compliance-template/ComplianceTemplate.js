import React from 'react';
import AppLayout from '../../layouts/app-layout/AppLayout';
import BreadcumbsComponent from '../../common/breadcumb/Breadcumb';

export default function ComplianceTemplate(props) {

    const { breadcumbsData } = props;

    return (
        <AppLayout>
            <BreadcumbsComponent data={breadcumbsData} />
            {props.children}
        </AppLayout>
    );
}