import { createSlice, createAsyncThunk, createAction } from '@reduxjs/toolkit'

/* Fetches the campaigns list */
export const fetchProjectFilterData = createAsyncThunk('global-dashboard/fetchProjectFilterData', async (params) => {
    const response = await axiosFetch.get('global/project-filter-data', {
        params: params
    })
    return response.data
})

export const updateSelectedProjects = createAction('global-dashboard/updateSelectedProjects')
