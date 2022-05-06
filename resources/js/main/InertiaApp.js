import React from 'react';
import { Provider } from 'react-redux'
import { render, ReactDOM } from 'react-dom'
import { createInertiaApp } from '@inertiajs/inertia-react'
import NProgress from 'nprogress'
import { Inertia } from '@inertiajs/inertia'

import configureStore from '../store/store'

const store = configureStore()

createInertiaApp({
  resolve: name => require(`../modules/${name}`),
  setup({ el, App, props }) {
    if(props.initialPage.props.tenancy_enabled){
      window.asset = (URL) => {
        if(appDiskDriver == 'local'){
          return `${appBaseURL}/tenancy/assets/${URL}`;
        }else{
          return `${appStorageURL}/public${URL}`;
        }
      };
    }
    else{
      window.asset = (URL) => {
        if(appDiskDriver == 'local'){
          return `${appBaseURL}/storage/${URL}`;
        }else{
          return `${appStorageURL}/public${URL}`;
        }

      };
    }
    
    render(
      <Provider store={store}>
        <App {...props} />
      </Provider>, el
    );
  }
});

//For Custom Loader
let timeout = null

NProgress.configure({ showSpinner: false, trickleSpeed: 300 });

Inertia.on('start', () => {
  timeout = setTimeout(() => NProgress.start(), 100)
})

Inertia.on('progress', (event) => {
  if (NProgress.isStarted() && event.detail.progress.percentage) {
    NProgress.set((event.detail.progress.percentage / 100) * 0.9)
  }
})

Inertia.on('finish', (event) => {
  clearTimeout(timeout)
  if (!NProgress.isStarted()) {
    return
  } else if (event.detail.visit.completed) {
    NProgress.done()
  } else if (event.detail.visit.interrupted) {
    NProgress.set(0)
  } else if (event.detail.visit.cancelled) {
    NProgress.done()
    NProgress.remove()
  }
})
