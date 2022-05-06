import { createAction, createReducer } from '@reduxjs/toolkit'
import {fetchCampaignList} from '../../actions/policy-management/campaigns'

const initialState = {
    status: 'idel',
    campaigns: []
}

const campaignReducer = createReducer(initialState, (builder) => {
  builder
    .addCase(fetchCampaignList.pending, (state, action) => {
        state.status = 'pending'
    })
    .addCase(fetchCampaignList.fulfilled, (state, action) => {
      state.campaigns = action.payload
      state.status = 'idel'
    })
})

export default campaignReducer
