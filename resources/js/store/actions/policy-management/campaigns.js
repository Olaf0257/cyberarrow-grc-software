import { createSlice, createAsyncThunk, createAction } from '@reduxjs/toolkit'

/* Fetches the campaigns list */
export const fetchCampaignList = createAsyncThunk('campaigns/fetchCampaignList', async (params) => {
    const response = await axiosFetch.get('policy-management/campaigns/list', {
        params: params
    })

    return response.data.success ? response.data.data : []
})

/* Delets the campaign by id */
export const deleteCampaigns = createAsyncThunk('campaigns/deleteCampaigns', async (id) => {
    const response = await axiosFetch.delete(`policy-management/campaigns/${id}/delete`)

    return response.data ? response.data : {}
})

/* Fetching campaign data by ID */
export const fetchCampaignDataByID =  createAsyncThunk('campaigns/fetchCampaignDataByID', async (id) => {
    const response = await axiosFetch.get(`/policy-management/campaigns/${id}/get-campaign-data`)

    return response.data ? response.data : {}
})


/* Duplicate campaign */
export const duplicateCampaigns = createAsyncThunk('campaigns/duplicateCampaigns', async ({campaignId, params}) => {
    const response = await axiosFetch.get(`/policy-management/campaigns/${campaignId}/get-campaign-data`, {
        params: params
    })

    return response.data ? response.data : {}
})

export const closeDuplicateCampaignModal = createAction('campaigns/duplicateCampaignModal/close')

/* Fetches the campaigns list */
export const fetchCampaignUserList = createAsyncThunk('campaigns/show/fetchCampaignUserList', async ({campaignId, params}) => {
    const response = await axiosFetch.get(`policy-management/campaigns/${campaignId}/render-users`, {
        params: params
    })

    return response.data.success ? response.data.data : []
})

/* Fetches the campaigns list */
export const fetchCampaignCreateData = createAsyncThunk('campaigns/fetchCampaignCreateData', async (params) => {
    const response = await axiosFetch.get(route("policy-management.campaigns.get-create-data"), {
        params: params
    })

    return response.data
})
