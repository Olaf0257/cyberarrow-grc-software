import React, { Fragment, useEffect, useState } from "react";
import { Link } from "@inertiajs/inertia-react";
import uniqueId from "lodash/uniqueId";
import styles from "./fc-more-event-popover.module.css";
import ReactTooltip from "react-tooltip";

function FcMoreEventPopover(props) {
    const {
        events,
        hide,
        title,
        pageCount,
        currentPage,
        loading,
        getMoreEventPopoverData,
        cellInfo,
        show,
    } = props;

    const [style, setStyle] = useState({
        display: "none",
    });

    // const [ajaxRunning,setAjaxRunning] = useState(false);

    /* Handle window resize */
    useEffect(() => {
        function handleResize() {
            hide();
        }

        window.addEventListener("resize", handleResize);
        return () => window.removeEventListener("resize", handleResize);
    }, []);

    useEffect(() => {

        function handleClickOutSide() {
            if (show) {
                hide();
            }
        }

        document.addEventListener("click", handleClickOutSide);

        if (show) {
            let relativePos = computeMorePopoverPosition();
            setStyle({
                display: "block",
                left: relativePos.left,
                top: relativePos.top - 35,
            });
        } else {
            setStyle({
                display: "none",
            });
        }

        return () => {
            document.removeEventListener("click", handleClickOutSide);
        };
    }, [show]);

    const computeMorePopoverPosition = () => {
        let parentPos = cellInfo.parentElPos;
        let childPos = cellInfo.childElPos;

        let relativePos = {};
        (relativePos.top = childPos.top - parentPos.top),
            (relativePos.right = childPos.right - parentPos.right),
            (relativePos.bottom = childPos.bottom - parentPos.bottom),
            (relativePos.left = childPos.left - parentPos.left);

        return relativePos;
    };

    const renderEvents = () => {
        return events.map((event, index) => {
            return (
                <Link
                    className="fc-day-grid-event fc-h-event fc-event fc-start fc-end"
                    href={event.url}
                    style={{
                        backgroundColor: event.backgroundColor,
                        color: event.textColor,
                    }}
                    key={uniqueId()}
                    data-tip={event.title}
                >
                    <div className="fc-content">
                        <span className="fc-title">{event.title}</span>
                    </div>
                </Link>
            );
        });
    };

    const renderLoaderSection = () => {
        return currentPage < pageCount ? (
            <div className="d-flex" style={{ height: 30 }}>
                <img
                    src={appBaseURL+"/assets/images/event_loading.gif"}
                    alt="loading"
                    height="30px"
                    style={{ margin: "0 auto" }}
                />
            </div>
        ) : (
            ""
        );
    };

    const handleScrolling = (element) => {
        let page = currentPage;

        /* not making further request if current page equals to page count */
        if (pageCount == currentPage) return;

        let reachedBottom =
        Math.abs(element.scrollTop) + Math.abs(element.clientHeight) + 1 >=
        Math.abs(element.scrollHeight);

        if (reachedBottom && !loading) {
            getMoreEventPopoverData(cellInfo.date, ++page);
        }
    };

    return (
        <Fragment>
            {show && (
                <div
                    className="fc-popover fc-more-popover "
                    style={{
                        ...style,
                        background: "#fff",
                    }}
                    onClick={(event) => {
                        event.stopPropagation();
                    }}
                >
                    <div className="fc-header fc-widget-header">
                        <span className="fc-title">{title}</span>
                        <span
                            className="fc-close fc-icon fc-icon-x"
                            onClick={() => {
                                hide();
                            }}
                        />
                    </div>
                    <div className={`fc-body fc-widget-content`}>
                        <div
                            className={`fc-event-container ${styles.fcEventContainer}`}
                            onScroll={(e) => handleScrolling(e.target)}
                        >
                            {renderEvents()}
                            {renderLoaderSection()}
                        </div>
                    </div>
                </div>
            )}
        </Fragment>
    );
}

export default FcMoreEventPopover;
