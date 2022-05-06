import React, { Fragment, useEffect, useRef, useState } from "react";
import FullCalendar from "@fullcalendar/react";
import dayGridPlugin from "@fullcalendar/daygrid";
import { useDispatch, useSelector } from "react-redux";
import { useDidMountEffect } from "../../../../custom-hooks";
import ReactTooltip from "react-tooltip";
import "./main.scss";
import {
    fetchCalendarData,
    fetchCalendarMorePopoverData,
    resetCalendarMorePopoverData,
} from "../../../../store/actions/global-dashboard/calendar";
import FcMoreEventPopover from "../../../../common/fc-more-event-popover/FcMoreEventPopover";
import { Inertia } from "@inertiajs/inertia";

function CalendarWidget(props) {
    const dispatch = useDispatch();
    const { selectedProjects } = useSelector(
        (store) => store.globalDashboardReducer.projectFilterReducer
    );
    const { calendarEvents } = useSelector(
        (store) => store.globalDashboardReducer.calendarReducer
    );
    const {
        calendarEvents: calendarMoreEvents,
        currentPage: calendarMoreEventCurrentPage,
        pageCount: calendarMoreEventPageCount,
        loading: calendarMoreEventLoading,
    } = useSelector(
        (store) => store.globalDashboardReducer.calendarMorePopoverReducer
    );
    const calendarRef = useRef(null);
    const [calendarApi, setCalendarApi] = useState(null);
    const [calendarPopoverTitle, setCalendarPopoverTitle] = useState("");
    const [currentCellInfo, setCurrentCellInfo] = useState(null);
    const [showMorePopover, setShowMorePopover] = useState(false);

    useDidMountEffect(() => {
        renderCalendarTasks();
    }, [selectedProjects]);

    useDidMountEffect(() => {
        setCalendarApi(calendarRef.current.getApi());
    }, [calendarRef.current]);

    const hideMorePopover = () => {
        dispatch(resetCalendarMorePopoverData());

        setShowMorePopover(false);
    };

    /* */
    const getMoreEventPopoverData = async (date, page) => {
        var month = date.getMonth() + 1;
        var current_month_date = date.getFullYear() + "-" + month + "-01";
        var date = date.getFullYear() + "-" + month + "-" + date.getDate();

        const dateOptions = {
            timeZone: "GMT",
            month: "long",
            day: "numeric",
            year: "numeric",
        };
        const dateFormatter = new Intl.DateTimeFormat("en-US", dateOptions);
        const dateAsFormattedString = dateFormatter.format(Date.parse(date));

        /* setting calendar popover title */
        setCalendarPopoverTitle(dateAsFormattedString);

        await dispatch(
            fetchCalendarMorePopoverData({
                projects: selectedProjects.join(","),
                current_date_month: current_month_date,
                date: date,
                page: page,
            })
        );
    };

    /* Renders calendar tasks dynamically */
    const renderCalendarTasks = async () => {
        var cdate = calendarApi.getDate();
        var month_int = cdate.getMonth() + 1;
        var current_month_date = cdate.getFullYear() + "-" + month_int + "-01";

        await dispatch(
            fetchCalendarData({
                projects: selectedProjects.join(","),
                current_date_month: current_month_date,
            })
        );
    };

    const handleEventPositioned = ({ el, event }) => {
        el.setAttribute("data-tip", event.title);
        ReactTooltip.rebuild();
    };

    return (
        <Fragment>
            <div className="calendar-div loader-overlay">
                <div className="row">
                    <div className="col-xl-12">
                        <div className="card">
                            <div className="task-calendar-main card-body border border-top-0 border-start-0 border-end-0 ">
                                <h5 className="head-text">
                                    <i className="fe-calendar" />
                                    &nbsp;Task Calendar
                                </h5>
                                <ul className="list-group list-group-horizontal  rect-div">
                                    <li className="list-group-item  rect">
                                        <span
                                            className="badge status-color"
                                            style={{ background: "#414141" }}
                                        >
                                            &nbsp;
                                        </span>
                                        <span className="status-text ms-1">
                                            Upcoming
                                        </span>
                                    </li>
                                    <li className="list-group-item  rect">
                                        <span
                                            className="badge status-color"
                                            style={{ background: "#5bc0de" }}
                                        >
                                            &nbsp;
                                        </span>
                                        <span className="status-text ms-1">
                                            Under Review
                                        </span>
                                    </li>
                                    <li className="list-group-item  rect">
                                        <span
                                            className="badge status-color"
                                            style={{ background: "#cf1110" }}
                                        >
                                            &nbsp;
                                        </span>
                                        <span className="status-text ms-1">
                                            Late
                                        </span>
                                    </li>
                                    <li className="list-group-item  rect">
                                        <span
                                            className="badge status-color"
                                            style={{ background: "#359f1d" }}
                                        >
                                            &nbsp;
                                        </span>
                                        <span className="status-text ms-1">
                                            Implemented
                                        </span>
                                    </li>
                                </ul>
                            </div>
                            <div className="card-body">
                                <FullCalendar
                                    customButtons={{
                                        prev: {
                                            text: "Prev",
                                            click: function () {
                                                calendarApi.prev();
                                                renderCalendarTasks();
                                            },
                                        },
                                        next: {
                                            text: "Next",
                                            click: function () {
                                                calendarApi.next();
                                                renderCalendarTasks();
                                            },
                                        },
                                        today: {
                                            text: "Today",
                                            click: function () {
                                                calendarApi.today();
                                                renderCalendarTasks();
                                            },
                                        },
                                    }}
                                    defaultView="dayGridMonth"
                                    plugins={[dayGridPlugin]}
                                    height={675}
                                    header={{
                                        left: "prev,next today",
                                        center: "title",
                                        right: "dayGridMonth,dayGridWeek",
                                    }}
                                    eventLimit={true}
                                    views={{
                                        agenda: {
                                            eventLimit: 4,
                                        },
                                        dayGrid: {
                                            eventLimit: 4,
                                        },
                                        day: {
                                            eventLimit: 4,
                                        },
                                    }}
                                    eventLimitText={() => "More"}
                                    events={calendarEvents}
                                    ref={calendarRef}
                                    eventLimitClick={async (cellInfo) => {
                                        let currentEventDate = cellInfo.date;
                                        let parentEl =
                                            calendarRef.current.elRef.current;
                                        let parentElPos =
                                            parentEl.getBoundingClientRect();
                                        let childElPos =
                                            cellInfo.moreEl.getBoundingClientRect();

                                        /* Setting currently clicked more popover trigger link*/
                                        setCurrentCellInfo({
                                            parentElPos: parentElPos,
                                            childElPos: childElPos,
                                            date: currentEventDate,
                                        });

                                        await getMoreEventPopoverData(
                                            currentEventDate,
                                            1
                                        );

                                        // /* showing popover and setting position and widht */
                                        setShowMorePopover(true);
                                    }}
                                    eventClick={({ event, jsEvent }) => {
                                        if(!event.classNames.includes('disabled_click')){
                                            jsEvent.cancelBubble = true;
                                            jsEvent.preventDefault();
    
                                            Inertia.visit(event.url);
                                        }
                                        else{
                                            jsEvent.preventDefault();
                                        }
                                    }}
                                    eventRender={({ event, element }) => {
                                        if(event.startEditable){
                                            event.remove();
                                        }
                                    }}
                                    eventPositioned={handleEventPositioned}
                                />
                                <FcMoreEventPopover
                                    events={calendarMoreEvents}
                                    hide={hideMorePopover}
                                    title={calendarPopoverTitle}
                                    pageCount={calendarMoreEventPageCount}
                                    currentPage={calendarMoreEventCurrentPage}
                                    loading={calendarMoreEventLoading}
                                    getMoreEventPopoverData={
                                        getMoreEventPopoverData
                                    }
                                    cellInfo={currentCellInfo}
                                    show={showMorePopover}
                                />
                                <ReactTooltip />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Fragment>
    );
}

export default CalendarWidget;
