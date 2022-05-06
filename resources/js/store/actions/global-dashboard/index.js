import { createSlice, createAsyncThunk, createAction } from '@reduxjs/toolkit'

/* Fetches the campaigns list */
export const fetchPageData = createAsyncThunk('global-dashboard/fetchPageData', async (params) => {
    const response = await axiosFetch.get('/global/dashboard/get-data', {
        params: params
    })
    return response.data
})
