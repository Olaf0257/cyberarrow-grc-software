import { createSlice, createAsyncThunk, createAction } from "@reduxjs/toolkit";

/* Fetches the data scope dropdown tree data */
export const fetchDataScopeDropdownTreeData = createAsyncThunk(
    "dataScope/fetchDropdownTreeData",
    async () => {
        const response = await axiosFetch.get("/data-scope/get-tree-view-data");

        return response.data.success ? response.data.data : [];
    }
);
