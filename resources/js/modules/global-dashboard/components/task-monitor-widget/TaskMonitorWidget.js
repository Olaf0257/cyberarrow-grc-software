import React from "react";
import { useSelector } from "react-redux";
import { Link } from "@inertiajs/inertia-react";

function TaskMonitorWidget(props) {
  const { selectedDepartment } = useSelector(
    (state) => state.commonReducer.departmentFilterReducer
  );
  const { selectedProjects } = useSelector(
    (store) => store.globalDashboardReducer.projectFilterReducer
  );
  const { allUpcomingTasks, allDueTodayTasks, allPassDueTasks } = props;

  return (
    <>
      <div className="py-5" id="tasks-monitor-widget">
        <div className="row align-items-center upcoming">
          <div className="col-7 offset-1 pe-0 d-flex align-items-center">
            <i data-feather="list" className="text-muted" style={{width:'25px',height:'25px'}}></i> <span className="mx-2 text-dark fa-2x" id="all-upcomming-tasks">{allUpcomingTasks}</span>
            <h5 className="text-muted">All Upcoming</h5>
          </div>
          <div className="col-4 ps-0">
            <Link
                href={`${appBaseURL}/global/tasks/all-active`}
                className="btn btn-light width-xs btn-rounded go-btn upcoming-go-btn"
                method="get"
                data={{
                  selected_departments: selectedDepartment.join(","),
                  selected_projects: selectedProjects.join(","),
                }}
            >
              Go
            </Link>
          </div>
        </div>
        <hr />
        <div className="row align-items-center due-today">
          <div className="col-7 offset-1 pe-0 d-flex align-items-center">
            <i data-feather="help-circle" className="text-muted" style={{width:'25px',height:'25px'}}></i>
              <span className="mx-2 text-dark fa-2x" id="all-upcomming-tasks">{allDueTodayTasks}</span>
              <h5 className="text-muted">Due Today</h5>
          </div>
          <div className="col-4 ps-0">
            <Link
                href={`${appBaseURL}/global/tasks/due-today`}
                className="btn btn-light width-xs btn-rounded go-btn"
                method="get"
                data={{
                  selected_departments: selectedDepartment.join(","),
                  selected_projects: selectedProjects.join(","),
                }}
            >
              Go
            </Link>
          </div>
        </div>
        <hr />
        <div className="row align-items-center past-due">
          <div className="col-7 offset-1 pe-0 d-flex align-items-center">
            <i data-feather="x-circle" className="text-muted" style={{width:'25px',height:'25px'}}></i>
              <span className="mx-2 text-dark  fa-2x" id="all-upcomming-tasks">{allPassDueTasks}</span>
              <h5 className="text-muted">Past Due</h5>
          </div>
          <div className="col-4 ps-0">
            <Link
                className="btn btn-light width-xs btn-rounded go-btn"
                href={`${appBaseURL}/global/tasks/pass-due`}
                method="get"
                data={{
                  selected_departments: selectedDepartment.join(","),
                  selected_projects: selectedProjects.join(","),
                }}
            >
              Go
            </Link>
          </div>
        </div>
      </div>
    </>
  );
}

export default TaskMonitorWidget;
