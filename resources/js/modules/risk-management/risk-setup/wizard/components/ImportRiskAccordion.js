import React, { Fragment, useEffect, useState } from "react";
import {Accordion, Card, Button} from 'react-bootstrap';
import { useAccordionButton } from 'react-bootstrap/AccordionButton';


function ImportRiskAccordion(props) {
  const { riskSelectedIds } = props;
  const [riskList, setProgressPercent] = useState({});
  const [selectedList, setSelectedList] = useState({});
  const [activeList, setActiveList] = useState({});
  const [toggleList, setToggleList] = useState([]);

  useEffect(async () => {
    // console.log("props.riskSelectedIds", props.riskSelectedIds);
    if (props.riskList) {
      setProgressPercent(props.riskList);
    }
    props.riskSelectedIds
      ? setSelectedList(props.riskSelectedIds)
      : setSelectedList();
    setActiveList({ selected: [] });
  }, [props]);

  const itemSelect = (e) => {
    props.clickCheckAction(e);
  };

  const toggleAccordion = (id) => {
    let selected = activeList.selected;
    let find = selected.indexOf(id);
    if (find > -1) {
      selected.splice(find, 1);
    } else {
      selected.push(id);
    }
    setActiveList({ selected });
  };

  function CustomToggle({ children, eventKey ,risk_id }) {
    
    const decoratedOnClick = useAccordionButton(eventKey, () =>{
      let selected = toggleList;
      let find = selected.indexOf(risk_id);
        if (find > -1) {
          selected.splice(find, 1);
        } else {
          selected.push(risk_id);
        }
        setToggleList(selected);
      }
    );
  
    return (
      <div
      onClick={decoratedOnClick}
      className="cursor-pointer"
    >
      <i
      className={
        toggleList.indexOf(risk_id) > -1
          ? "icon fas fa-chevron-right expand-icon-w"
          : "icon fas fa-chevron-down expand-icon-w"

      }
    />
    <span className="risk-register-title ms-2">{children}</span>
    </div>
    );
  }

  return (
    <Fragment>
      {riskList.risks ? (
        riskList.risks.data.length > 0 ? (
          riskList.risks.data.map(function (eachRisk, index) {
            return (
              <div
                key={eachRisk.id}
                className="border-bottom-thin-1 mt-10"
                id="accordion"
              >
                    <Accordion defaultActiveKey={"risk-category-wp_" + eachRisk.id}>
                      <div className="mb-0">
                        <div className="d-flex align-items-center">
                          <CustomToggle eventKey={"risk-category-wp_" + eachRisk.id} risk={eachRisk.id}>
                          {eachRisk["name"]}
                          </CustomToggle>
                          <div className="items__num ms-auto pt-3">
                            <div className="checkbox checkbox-success descrip__checkbox">
                              <input
                                id={"risk_item_checkbox" + eachRisk.id}
                                name="risk-item-checkbox[]"
                                type="checkbox"
                                value={eachRisk.id}
                                onChange={() => itemSelect(eachRisk.id)}
                                checked={selectedList.includes(eachRisk.id)}
                              />
                              <label
                                htmlFor={"risk_item_checkbox" + eachRisk.id}
                              ></label>
                            </div>
                          </div>
                        </div>
                        <Accordion.Collapse eventKey={"risk-category-wp_" + eachRisk.id}>
                          <Card.Body className="px-0 pt-0 pb-1">{eachRisk["risk_description"]}</Card.Body>
                        </Accordion.Collapse>
                      </div>
                    </Accordion>
                </div>
            );
          })
        ) : (
          <div className="d-flex justify-content-center p-2 text-secondary custom-no-result-background">
            {riskList.risks.srchKeyword
              ? "No result found for: " + riskList.risks.srchKeyword
              : "No result found"}
          </div>
        )
      ) : (
        "No data found"
      )}
    </Fragment>
  );
}

export default ImportRiskAccordion;
