import { createSlice, createAsyncThunk, createAction } from '@reduxjs/toolkit'

/* Fetches the fetchDashboardData */
export const fetchDashboardData = createAsyncThunk('riskManagement/dashboard/fetchDashboardData', async ({params}) => {
    const response = await axiosFetch.get(`risks/dashboard/dashboard-data`, {
        params: params
    })

    return response.data
})
