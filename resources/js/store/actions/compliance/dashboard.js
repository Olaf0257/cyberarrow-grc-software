import { createSlice, createAsyncThunk, createAction } from '@reduxjs/toolkit'

/* Fetches the calendar */
export const fetchCalendarData = createAsyncThunk('compliance/dashboard/fetchCalendarData', async (params) => {
    const response = await axiosFetch.get('compliance/dashboard/calendar-data', {
        params: params
    })

    return response.data
})




/* Fetches the calendar more popover data */
export const fetchCalendarMorePopoverData = createAsyncThunk('compliance/dashboard/fetchCalendarMorePopoverData', async (params) => {
    const response = await axiosFetch.get('compliance/dashboard/get-calendar-more-popover-data', {
        params: params
    })

    return response.data
})

/* Reset more popover data */
export const resetCalendarMorePopoverData = createAction('compliance/dashboard/resetCalendarMorePopoverData')
