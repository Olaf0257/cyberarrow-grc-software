import React, { Fragment, useEffect, useState } from "react";
import ReactCollapsingTable from "react-collapsing-table";
import { useSelector } from "react-redux";
import ContentLoader from "../content-loader/ContentLoader";
import { useStateIfMounted } from "use-state-if-mounted";
import DataTableHeader from "./DataTableHeader";
import DataTableFooter from "./DataTableFooter";
import "./data-table.css";

function DataTable(props) {
    const appDataScope = useSelector(
        (state) => state.appDataScope.selectedDataScope.value
    );
    const {
        columns,
        fetchURL,
        ajaxData,
        refresh,
        search,
        loaderHeight,
        offlineData,
        triggeredLength,
        hideHeader = false,
        defaultPage,
        refreshOnPageChange,
    } = props;

    const [loading, setLoading] = useStateIfMounted(false);
    const [data, setData] = useStateIfMounted({ data: "" });
    const [localData, setLocalData] = useStateIfMounted([]);
    const [currentPage, setCurrentPage] = useState(1);
    const [perPage, setPerPage] = useState(10);
    const [filterText, setFilterText] = useState("");
    const [isOffline, setIsOffline] = useState(false);
    const [activeArrayIndex, setActiveArrayIndex] = useState(0);

    const fetchData = async (page, size = perPage, search = filterText) => {
        let nextPage =
            typeof props.defaultPage == "undefined" ? page : props.defaultPage;
        let response = await axiosFetch.get(
            fetchURL +
            "?data_scope=" +
            appDataScope +
            "&page=" +
            nextPage +
            "&per_page=" +
            size +
            "&search=" +
            search,
            {
                params: ajaxData,
            }
        );
        if (response.status === 200) {
            setData(response.data.data);
            setLoading(false);
        }
    };

    const manageOfflineData = (data, activeIndex = 0, filter = false) => {
        if (!filter) {
            setLocalData(data);
        }
        let paginatedDataObject = [];
        var i, j, temporary;
        for (i = 0, j = data.length; i < j; i += perPage) {
            temporary = data.slice(i, i + perPage);
            paginatedDataObject.push(temporary);
        }
        var finalData = {
            data: paginatedDataObject[activeIndex],
        };
        if (finalData.data != null) {
            setData(finalData);
        }
        else {
            setData('');
        }
        setLoading(false);
    };

    useEffect(async () => {
        setLoading(true);
        if (offlineData != null) {
            setIsOffline(true);
            if (offlineData.length > 0) {
                manageOfflineData(offlineData);
            } else {
                setLoading(false);
                setData({ data: "" });
            }
        } else {
            fetchData(currentPage, perPage, filterText);
        }
    }, [ajaxData, refresh, triggeredLength, props.defaultPage]);

    const onPerPageChange = async (value) => {
        setLoading(true);
        setPerPage(value);
        if (isOffline) {
            manageOfflineData(localData);
        } else {
            fetchData(1, value, filterText);
        }
    };

    const onSearchChange = async (value) => {
        if (isOffline) {
            var filterData = localData.filter((eachData) => {
                for (const each in eachData) {
                    if (eachData[each].includes(value)) {
                        return eachData;
                    }
                }
            });
            manageOfflineData(filterData, 0, true);
        } else {
            setLoading(true);
            setFilterText(value);
            fetchData(1, perPage, value);
        }
    };

    const paginationLinkedClickAction = (e) => {
        e.preventDefault()
        if (isOffline) {
            let activeIndex = e - 1;
            var allData = localData;
            setActiveArrayIndex(activeIndex);
            manageOfflineData(allData, activeIndex);
        } else {
            if (refreshOnPageChange) {
                setLoading(true);
            }
            const url = new URL(e.target.dataset.link);
            const page = url.searchParams.get("page");
            setCurrentPage(page);
            fetchData(page, perPage, filterText);
        }
    };

    const paginationEventSuper = (e) => {
        const url = new URL(e.target.dataset.link);
        const page = url.searchParams.get("page");
        if (props.paginationEvent) {
            props.paginationEvent(page);
        }
    };

    return (
        <Fragment>
            {!hideHeader && (
                <DataTableHeader
                    perPageChangedTo={(value) => onPerPageChange(value)}
                    searchChangedTo={(value) => onSearchChange(value)}
                    enableSearch={search}
                />
            )}

            <ContentLoader show={loading}>
                {loading ? (
                    <div style={{ height: "535px" }}>Loading . . .</div>
                ) : (
                    <ReactCollapsingTable
                        rows={data ? data.data : ""}
                        columns={columns}
                        rowSize={perPage}
                        column="id"
                        icons={{
                            openRow: (
                                <svg
                                    width="20"
                                    height="20"
                                    version="1.1"
                                    id="Layer_1"
                                    xmlns="http://www.w3.org/2000/svg"
                                    xmlnsXlink="http://www.w3.org/1999/xlink"
                                    x="0px"
                                    y="0px"
                                    viewBox="0 0 122.88 119.72"
                                    style={{
                                        enableBackground:
                                            "new 0 0 122.88 119.72",
                                        fill: "#6c757d",
                                        marginRight: "3px",
                                    }}
                                    xmlSpace="preserve"
                                >
                                    <g>
                                        <path d="M22.72,0h77.45c6.25,0,11.93,2.56,16.05,6.67c4.11,4.11,6.67,9.79,6.67,16.05v74.29c0,6.25-2.56,11.93-6.67,16.05 l-0.32,0.29c-4.09,3.94-9.64,6.38-15.73,6.38H22.72c-6.25,0-11.93-2.56-16.05-6.67l-0.3-0.32C2.43,108.64,0,103.09,0,97.01V22.71 c0-6.25,2.55-11.93,6.67-16.05C10.78,2.55,16.46,0,22.72,0L22.72,0z M55.47,38.34c0-3.3,2.67-5.97,5.97-5.97 c3.3,0,5.97,2.67,5.97,5.97v15.55h15.55c3.3,0,5.97,2.67,5.97,5.97c0,3.3-2.67,5.97-5.97,5.97H67.41v15.55 c0,3.3-2.67,5.97-5.97,5.97c-3.3,0-5.97-2.67-5.97-5.97V65.83H39.91c-3.3,0-5.97-2.67-5.97-5.97c0-3.3,2.67-5.97,5.97-5.97h15.55 V38.34L55.47,38.34z M100.16,10.24H22.72c-3.43,0-6.54,1.41-8.81,3.67c-2.26,2.26-3.67,5.38-3.67,8.81v74.29 c0,3.33,1.31,6.35,3.43,8.59l0.24,0.22c2.26,2.26,5.38,3.67,8.81,3.67h77.45c3.32,0,6.35-1.31,8.59-3.44l0.21-0.23 c2.26-2.26,3.67-5.38,3.67-8.81V22.71c0-3.42-1.41-6.54-3.67-8.81C106.71,11.65,103.59,10.24,100.16,10.24L100.16,10.24z" />
                                    </g>
                                </svg>
                            ),
                            closeRow: (
                                <svg
                                    width="20"
                                    height="20"
                                    version="1.1"
                                    id="Layer_1"
                                    xmlns="http://www.w3.org/2000/svg"
                                    xmlnsXlink="http://www.w3.org/1999/xlink"
                                    x="0px"
                                    y="0px"
                                    viewBox="0 0 122.88 119.72"
                                    style={{
                                        enableBackground:
                                            "new 0 0 122.88 119.72",
                                        fill: "#6c757d",
                                        marginRight: "3px",
                                    }}
                                    xmlSpace="preserve"
                                >
                                    <g>
                                        <path d="M22.72,0h77.45c6.25,0,11.93,2.56,16.05,6.67c4.11,4.11,6.67,9.79,6.67,16.05v74.29c0,6.25-2.56,11.93-6.67,16.05 l-0.32,0.29c-4.09,3.94-9.64,6.38-15.73,6.38H22.72c-6.25,0-11.93-2.56-16.05-6.67l-0.3-0.32C2.43,108.64,0,103.09,0,97.01V22.71 c0-6.25,2.55-11.93,6.67-16.05C10.78,2.55,16.46,0,22.72,0L22.72,0z M39.92,65.83c-3.3,0-5.97-2.67-5.97-5.97 c0-3.3,2.67-5.97,5.97-5.97h43.05c3.3,0,5.97,2.67,5.97,5.97c0,3.3-2.67,5.97-5.97,5.97H39.92L39.92,65.83z M100.16,10.24H22.72 c-3.43,0-6.55,1.41-8.81,3.67c-2.26,2.26-3.67,5.38-3.67,8.81v74.29c0,3.33,1.31,6.35,3.43,8.59l0.24,0.22 c2.26,2.26,5.38,3.67,8.81,3.67h77.45c3.32,0,6.35-1.31,8.59-3.44l0.21-0.23c2.26-2.26,3.67-5.38,3.67-8.81V22.71 c0-3.42-1.41-6.54-3.67-8.81C106.71,11.65,103.59,10.24,100.16,10.24L100.16,10.24z" />
                                    </g>
                                </svg>
                            ),
                        }}
                    />
                )}
            </ContentLoader>
            <DataTableFooter
                data={data}
                paginateTo={(value) => paginationLinkedClickAction(value)}
                paginateEventTrigger={(value) => paginationEventSuper(value)}
                isOffline={isOffline}
                perpage={perPage}
                activeIndex={activeArrayIndex}
                total={offlineData != null ? offlineData.length : 0}
            />
        </Fragment>
    );
}

export default DataTable;
