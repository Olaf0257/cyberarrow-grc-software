import React, { Fragment, useEffect, useState } from "react";
import { Link } from "@inertiajs/inertia-react";
import { Breadcrumb } from "react-bootstrap";

function BreadcumbComponent(props) {
    const [breadcumbsData, setbreadcumbsData] = useState([]);

    useEffect(async () => {
        if (props.data) {
            setbreadcumbsData(props.data);
        }
    }, [props]);

    return (
        <Fragment>
            {/* Breadcomb Components */}
            {breadcumbsData.breadcumbs ? (
                <div className="row">
                    <div className="col-12">
                        <div className="page-title-box">
                            <div className="page-title-right">
                                <ol className="breadcrumb m-0">
                                    {breadcumbsData.breadcumbs.map(function (
                                        item,
                                        index
                                    ) {
                                        return item.href.length > 0 ? (
                                            <li
                                                key={index}
                                                className="breadcrumb-item"
                                            >
                                                <Link href={item.href}>
                                                    {item.title}
                                                </Link>
                                            </li>
                                        ) : (
                                            <li
                                                key={index}
                                                className="breadcrumb-item active"
                                            >
                                                <a href="#">{item.title}</a>
                                            </li>
                                        );
                                    })}
                                </ol>
                                {/* <Breadcrumb>
                                    {breadcumbsData.breadcumbs.map(function (
                                        item,
                                        index
                                    ) {
                                        return item.href.length > 0 ? (
                                            <Breadcrumb.Item
                                                key={index}
                                                href={item.href}
                                            >
                                                {item.title}
                                            </Breadcrumb.Item>
                                        ) : (
                                            <Breadcrumb.Item key={index} active>
                                                {item.title}
                                            </Breadcrumb.Item>
                                        );
                                    })}
                                </Breadcrumb> */}
                            </div>
                            <h4 className="page-title">
                                {breadcumbsData.title}
                            </h4>
                        </div>
                    </div>
                </div>
            ) : (
                ""
            )}
        </Fragment>
    );
}

export default BreadcumbComponent;
