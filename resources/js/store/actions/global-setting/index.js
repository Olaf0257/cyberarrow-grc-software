import { createSlice, createAsyncThunk, createAction } from '@reduxjs/toolkit'

/* Fetches the globalSettings */
export const globalSetting = createAsyncThunk('globalSetting', async (params) => {
    const response = await axiosFetch.get('globalSettings', {
        params: params
    })

    return response.data.success ? response.data.data : []
})
