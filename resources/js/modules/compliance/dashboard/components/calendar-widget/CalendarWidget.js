import React, { Fragment, useState, useEffect, useRef } from "react";
import FullCalendar from "@fullcalendar/react"; // must go before plugins
import dayGridPlugin from "@fullcalendar/daygrid";
import "./main.scss";
import ReactTooltip from "react-tooltip";
import { useDispatch, useSelector } from "react-redux";
import {
    fetchCalendarData,
    fetchCalendarMorePopoverData,
    resetCalendarMorePopoverData,
} from "../../../../../store/actions/compliance/dashboard";
import FcMoreEventPopover from "../../../../../common/fc-more-event-popover/FcMoreEventPopover";
import { Inertia } from "@inertiajs/inertia";

function CalendarWidget(props) {
    const calendarRef = useRef(null);
    const [calendarApi, setCalendarApi] = useState(null);
    const [currentCellInfo, setCurrentCellInfo] = useState(null);
    const appDataScope = useSelector(
        (store) => store.appDataScope.selectedDataScope.value
    );
    const { calendarEvents } = useSelector(
        (store) => store.complianceReducer.dashboardCalendarReducer
    );
    const {
        calendarEvents: calendarMoreEvents,
        currentPage: calendarMoreEventCurrentPage,
        pageCount: calendarMoreEventPageCount,
        loading: calendarMoreEventLoading,

    } = useSelector(
        (store) => store.complianceReducer.dashboardCalendarMorePopoverReducer
    );
    const [showMorePopover, setShowMorePopover] = useState(false);
    const [calendarPopoverTitle, setCalendarPopoverTitle] = useState("");
    const dispatch = useDispatch();

    useEffect(() => {
        renderCalendarTasks();
    }, [appDataScope]);

    const renderCalendarTasks = () => {
        if (!calendarApi) {
            var calendarApi = calendarRef.current.getApi();

            /* Setting calendarApi */
            setCalendarApi(calendarApi);
        }

        var cdate = calendarApi.getDate();
        var month_int = cdate.getMonth() + 1;
        var current_month_date = cdate.getFullYear() + "-" + month_int + "-01";

        dispatch(
            fetchCalendarData({
                current_date_month: current_month_date,
                data_scope: appDataScope,
            })
        );
    };

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
                current_date_month: current_month_date,
                date: date,
                page: page,
            })
        );
    };

    const handleEventPositioned = ({ el, event }) => {
        el.setAttribute("data-tip", event.title);
        ReactTooltip.rebuild();
    };

    return (
        <Fragment>
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
                            <span className="status-text ms-1">Upcoming</span>
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
                            <span className="status-text ms-1">Late</span>
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
                        ref={calendarRef}
                        defaultView="dayGridMonth"
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
                        eventLimitText={(el) => {
                            return "More";
                        }}
                        events={calendarEvents}
                        eventLimitClick={async (cellInfo, jsEvent, view) => {
                            let currentEventDate = cellInfo.date;
                            let parentEl = calendarRef.current.elRef.current;
                            let parentElPos = parentEl.getBoundingClientRect();
                            let childElPos =
                                cellInfo.moreEl.getBoundingClientRect();

                            /* Setting currently clicked more popover trigger link*/
                            setCurrentCellInfo({
                                parentElPos: parentElPos,
                                childElPos: childElPos,
                                date: currentEventDate,
                            });

                            await getMoreEventPopoverData(currentEventDate, 1);

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
                        eventPositioned={handleEventPositioned}
                    />
                    <FcMoreEventPopover
                        events={calendarMoreEvents}
                        hide={hideMorePopover}
                        title={calendarPopoverTitle}
                        pageCount={calendarMoreEventPageCount}
                        currentPage={calendarMoreEventCurrentPage}
                        loading={calendarMoreEventLoading}
                        getMoreEventPopoverData={getMoreEventPopoverData}
                        cellInfo={currentCellInfo}
                        show={showMorePopover}
                    />
                    <ReactTooltip />
                </div>
            </div>
        </Fragment>
    );
}

export default CalendarWidget;
