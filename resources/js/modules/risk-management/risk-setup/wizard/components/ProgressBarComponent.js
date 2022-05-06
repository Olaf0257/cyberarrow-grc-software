import React, { Fragment, useEffect, useState } from "react";

function ProgressBarComponent(props) {
  const { reachedWizardTab } = props;
  const [progressPercent, setProgressPercent] = useState({});

  useEffect(() => {
    if (reachedWizardTab == 1) {
      setProgressPercent("33");
    }
    if (reachedWizardTab == 2) {
      setProgressPercent("66");
    }

    if (reachedWizardTab == 3) {
      setProgressPercent("100");
    }
  }, [reachedWizardTab]);

  return (
    <Fragment>
      {progressPercent > 0 ? (
        <div id="bar" className="progress mb-2" style={{ height: "7px" }}>
          <div
            id="risk-setup-progress-bar"
            className="bar progress-bar progress-bar-striped progress-bar-animated secondary-bg-color risk-progress-bar"
            role="progressbar"
            style={{ width: progressPercent + "%" }}
            aria-valuenow="25"
            aria-valuemin="0"
            aria-valuemax="100"
          ></div>
        </div>
      ) : (
        "No progress percent"
      )}
    </Fragment>
  );
}

export default ProgressBarComponent;
