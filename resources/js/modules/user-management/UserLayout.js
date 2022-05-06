import React from 'react';
import AppLayout from '../../layouts/app-layout/AppLayout';
import BreadcumbsComponent from '../../common/breadcumb/Breadcumb';
import './styles/style.scss';

export default function UserLayout(props) {

    const { breadcumbsData } = props;

    return (
        <AppLayout>
            <BreadcumbsComponent data={breadcumbsData} />
            <div id="user-layout">
                {props.children}
            </div>
        </AppLayout>
    );
}