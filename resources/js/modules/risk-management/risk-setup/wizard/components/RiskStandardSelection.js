import React, { Fragment, useEffect, useState } from "react";
import { defaultMemoize } from "reselect";

function RiskStandardSelection(props) {
    const [standards, setprojectData] = useState([]);
    const [inputName, setInputName] = useState([]);

    useEffect(async () => {
        if (props.riskStandards) {
            setprojectData(props.riskStandards);
        }
        if (props.inputName) {
            setInputName(props.inputName);
        }
    }, [props]);

    const selectItem = (e) => {
        props.selectStandard
            ? props.selectStandard(e)
            : props.selectApproach(e);
    };

    return (
        <Fragment>
            {standards
                ? standards.map(function (datum, index) {
                      return (
                          <div
                              key={index}
                              className="col-xl-6 col-lg-6 col-md-6 standard-box"
                          >
                              <div className="card">
                                  <div className="card-body project-box br-dark">
                                      <div className="head-text text-center">
                                          <h4>{datum.standardName}</h4>
                                          <p className="my-3 iso-subtext">
                                              {datum.description}
                                          </p>
                                          <div className="checkbox-btn">
                                              <input
                                                  type="radio"
                                                  name={inputName}
                                                  onClick={selectItem}
                                                  defaultValue={datum.value}
                                                  aria-label={datum.value}
                                                  id={datum.value}
                                              />
                                              <label htmlFor={datum.value}>
                                                  Choose
                                              </label>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                          </div>
                      );
                  })
                : ""}
        </Fragment>
    );
}

export default RiskStandardSelection;
