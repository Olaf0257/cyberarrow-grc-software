import { createSlice, createAsyncThunk, createAction } from '@reduxjs/toolkit'

/* Fetches the calendar data list */
export const fetchCalendarData = createAsyncThunk('global-dashboard/fetchCalendarData', async (params) => {
    const response = await axiosFetch.get('global/dashboard/get-calendar-data', {
        params: params
    })

    return response.data
})

/* Fetches the calendar paginated data list */
export const fetchCalendarMorePopoverData = createAsyncThunk('global-dashboard/fetchCalendarMorePopoverData', async (params) => {
    //get-calendar-data
    const response = await axiosFetch.get('global/dashboard/get-calendar-more-popover-data', {
        params: params
    })

    return response.data
})

/* Reset more popover data */
export const resetCalendarMorePopoverData = createAction('global-dashboard/resetCalendarMorePopoverData')
