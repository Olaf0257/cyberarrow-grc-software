import { createAction, createReducer } from "@reduxjs/toolkit";
import { fetchDataScopeDropdownTreeData } from "../actions/data-scope-dropdown";

const update = createAction("datascope/update");

const initialState = {
    selectedDataScope: getInitialDataScope(),
    dropdownTreeData: [],
};

/* Search data scope tree matching the value and returns it */
const searchTree = (element, matchingValue) => {
    if (element.value == matchingValue) {
        return element;
    } else if (element.children != null) {
        var i;
        var result = null;
        for (i = 0; result == null && i < element.children.length; i++) {
            result = searchTree(element.children[i], matchingValue);
        }
        return result;
    }
    return null;
};

const appScopeReducer = createReducer(initialState, (builder) => {
    builder
        .addCase(update, (state, action) => {
            state.selectedDataScope = action.payload;
        })
        .addCase(fetchDataScopeDropdownTreeData.fulfilled, (state, action) => {
            let treeData = action.payload;

            /* Updating Local store on data structure update data */
            if (treeData.length > 0) {
                let localStorageDataScope = localStorage.getItem("data-scope");

                /* Enters if when user first logged in */
                if (!localStorageDataScope) {
                    let { value, label } = treeData[0];

                    localStorage.setItem(
                        "data-scope",
                        JSON.stringify({ value: value, label: label })
                    );
                } else {
                    let updatedSelectedNode = searchTree(
                        treeData[0],
                        JSON.parse(localStorageDataScope).value
                    );

                    if (updatedSelectedNode) {
                        let { value, label } = updatedSelectedNode;

                        localStorage.setItem(
                            "data-scope",
                            JSON.stringify({ value: value, label: label })
                        );
                    }
                }

                /* Updating the selected data scope */
                state.selectedDataScope = getInitialDataScope();
            }

            state.dropdownTreeData = treeData;
        });
});

export default appScopeReducer;
