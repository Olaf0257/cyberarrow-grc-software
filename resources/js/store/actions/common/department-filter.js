import { createSlice, createAsyncThunk, createAction } from '@reduxjs/toolkit'

/* Fetches the campaigns list */
export const fetchDepartmentFilterData = createAsyncThunk('global-dashboard/fetchDepartmentFilterData', async (params) => {
    const response = await axiosFetch.get(route('common.department-filter-tree-view-data'), {
        params: params
    })
    return response.data
})

export const updateSelectedDepartments = createAction('global-dashboard/updateSelectedDepartments')



