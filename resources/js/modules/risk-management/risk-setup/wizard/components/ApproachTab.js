import React from "react";
const approachData = [
  {
    name: "Automated",
    description:
      "The automated approach will automatically import and map risks and controls against chosen standard based on an active compliance project.",
    value: "Automated",
  },
  {
    name: "Yourself",
    description:
      "Choose this approach if you want to select the risks manually based on the chosen standard.",
    value: "Yourself",
  },
];

const ApproachTab = (props) => {
  const { setSelectedApproach, projectExist } = props;
  const handleSelectApproach = (approach) => {
    setSelectedApproach(approach);
  };

  const checkDisabled = (approach) =>
    approach == "Automated" ? !projectExist : false;

  return (
    <>
      {approachData.map((approach, index) => {
        return (
          <div key={index} className="col-xl-6 col-lg-6 col-md-6 standard-box">
            <div className="card">
              <div className="card-body project-box br-dark">
                <div className="head-text text-center">
                  <h4>{approach.name}</h4>
                  <p className="my-3 iso-subtext">{approach.description}</p>
                  <div className="checkbox-btn">
                    <input
                      type="radio"
                      defaultValue={approach.value}
                      aria-label={approach.value}
                      id={approach.value}
                      name="risk-setup-approach"
                      onClick={(e) => {
                        handleSelectApproach(e.target.value);
                      }}
                      disabled={checkDisabled(approach.value)}
                    />
                    <label htmlFor={approach.value}>Choose</label>
                  </div>
                </div>
              </div>
            </div>
          </div>
        );
      })}
    </>
  );
};
export default ApproachTab;
