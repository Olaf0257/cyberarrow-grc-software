import React, { Fragment, useEffect, useState } from "react";
import { useDispatch, useSelector } from "react-redux";
import TreeSelect, { SHOW_PARENT } from "rc-tree-select";
import "rc-tree-select/assets/index.less";
import "./data-scope-dropdown.scss";
import {showToastMessage} from "../../../../../utils/toast-message";

function DataScopeDropdown(props) {
    const treeData = useSelector(
        (store) => store.appDataScope.dropdownTreeData
    );
    const selectedDataScope = useSelector(
        (state) => state.appDataScope.selectedDataScope
    );
    const dispatch = useDispatch();
    const onChange = (value, label) => {
        let updatedDataScope = { label: label[0], value: value };
        /* Setting selected to localstorage */
        localStorage.setItem("data-scope", JSON.stringify(updatedDataScope));
        /* Updating the data scope in store */
        dispatch({ type: "datascope/update", payload: updatedDataScope });

        showToastMessage( 'Organization changed successfully!','Success');
    };

    return (
        <Fragment>
            <div
                id="data-scope-dropdown"
                style={{ minWidth: 100, maxWidth: 200, float: "right" }}
            >
                {treeData && (
                    <TreeSelect
                        value={selectedDataScope}
                        dropdownStyle={{
                            zIndex: "1002",
                            position: "fixed",
                            cursor: "pointer",
                        }}
                        dropdownMatchSelectWidth
                        treeLine="true"
                        treeDefaultExpandAll
                        treeIcon="&nbsp;"
                        style={{ width: 140 }}
                        treeData={treeData}
                        onChange={onChange}
                        animation="slide-up"
                        dropdownClassName="data-scope-dropdown-menu"
                    />
                )}
            </div>
        </Fragment>
    );
}

export default DataScopeDropdown;
