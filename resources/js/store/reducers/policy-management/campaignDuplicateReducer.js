import { createAction, createReducer } from '@reduxjs/toolkit'
import {duplicateCampaigns, closeDuplicateCampaignModal} from '../../actions/policy-management/campaigns'

const initialState = {
    status: 'idel',
    campaign: null,
    showModal: false
}

const campaignDuplicateReducer = createReducer(initialState, (builder) => {
  builder
    .addCase(duplicateCampaigns.fulfilled, (state, action) => {
        if (action.payload.success) {
            state.campaign = action.payload.data
            state.showModal = true
        }
    })
    .addCase(closeDuplicateCampaignModal, (state, action) => {
        state.showModal = false
    })
})

export default campaignDuplicateReducer
