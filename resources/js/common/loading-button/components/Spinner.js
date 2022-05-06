import React from 'react';

function  Spinner(props) {
    return (
        <span style={{
            position: 'absolute',
            zIndex: 2,
            display: 'inline-block',
            width: '32px',
            height: '32px',
            top: '50%',
            marginTop: 0,
            opacity: 0,
            pointerEvents: 'none'
        }}>
            <div role="progressbar" style={{position: 'absolute', width: '100px', zIndex: 'auto', left: 'auto', top: 'auto'}}>
                <div style={{position: 'absolute', top: '-1px', opacity: '0.25', animation: '1s linear 0s infinite normal none running opacity-100-25-0-12'}}>
                    <div style={{position: 'absolute', width: '5.6px', height: 2, background: 'rgb(221, 221, 221)', boxShadow: 'rgba(0, 0, 0, 0.1) 0px 0px 1px', transformOrigin: 'left center', transform: 'rotate(0deg) translate(6px, 0px)', borderRadius: 1}} /></div>
                    <div style={{position: 'absolute', top: '-1px', opacity: '0.25', animation: '1s linear 0s infinite normal none running opacity-100-25-1-12'}}>
                        <div style={{position: 'absolute', width: '5.6px', height: 2, background: 'rgb(221, 221, 221)', boxShadow: 'rgba(0, 0, 0, 0.1) 0px 0px 1px', transformOrigin: 'left center', transform: 'rotate(30deg) translate(6px, 0px)', borderRadius: 1}} /></div>
                        <div style={{position: 'absolute', top: '-1px', opacity: '0.25', animation: '1s linear 0s infinite normal none running opacity-100-25-2-12'}}>
                            <div style={{position: 'absolute', width: '5.6px', height: 2, background: 'rgb(221, 221, 221)', boxShadow: 'rgba(0, 0, 0, 0.1) 0px 0px 1px', transformOrigin: 'left center', transform: 'rotate(60deg) translate(6px, 0px)', borderRadius: 1}} /></div>
                        <div style={{position: 'absolute', top: '-1px', opacity: '0.25', animation: '1s linear 0s infinite normal none running opacity-100-25-3-12'}}>
                            <div style={{position: 'absolute', width: '5.6px', height: 2, background: 'rgb(221, 221, 221)', boxShadow: 'rgba(0, 0, 0, 0.1) 0px 0px 1px', transformOrigin: 'left center', transform: 'rotate(90deg) translate(6px, 0px)', borderRadius: 1}} /></div>
                        <div style={{position: 'absolute', top: '-1px', opacity: '0.25', animation: '1s linear 0s infinite normal none running opacity-100-25-4-12'}}>
                            <div style={{position: 'absolute', width: '5.6px', height: 2, background: 'rgb(221, 221, 221)', boxShadow: 'rgba(0, 0, 0, 0.1) 0px 0px 1px', transformOrigin: 'left center', transform: 'rotate(120deg) translate(6px, 0px)', borderRadius: 1}} /></div>
                            <div style={{position: 'absolute', top: '-1px', opacity: '0.25', animation: '1s linear 0s infinite normal none running opacity-100-25-5-12'}}>
                                <div style={{position: 'absolute', width: '5.6px', height: 2, background: 'rgb(221, 221, 221)', boxShadow: 'rgba(0, 0, 0, 0.1) 0px 0px 1px', transformOrigin: 'left center', transform: 'rotate(150deg) translate(6px, 0px)', borderRadius: 1}} /></div>
                                <div style={{position: 'absolute', top: '-1px', opacity: '0.25', animation: '1s linear 0s infinite normal none running opacity-100-25-6-12'}}><div style={{position: 'absolute', width: '5.6px', height: 2, background: 'rgb(221, 221, 221)', boxShadow: 'rgba(0, 0, 0, 0.1) 0px 0px 1px', transformOrigin: 'left center', transform: 'rotate(180deg) translate(6px, 0px)', borderRadius: 1}} /></div>
                                <div style={{position: 'absolute', top: '-1px', opacity: '0.25', animation: '1s linear 0s infinite normal none running opacity-100-25-7-12'}}><div style={{position: 'absolute', width: '5.6px', height: 2, background: 'rgb(221, 221, 221)', boxShadow: 'rgba(0, 0, 0, 0.1) 0px 0px 1px', transformOrigin: 'left center', transform: 'rotate(210deg) translate(6px, 0px)', borderRadius: 1}} /></div>
                                <div style={{position: 'absolute', top: '-1px', opacity: '0.25', animation: '1s linear 0s infinite normal none running opacity-100-25-8-12'}}><div style={{position: 'absolute', width: '5.6px', height: 2, background: 'rgb(221, 221, 221)', boxShadow: 'rgba(0, 0, 0, 0.1) 0px 0px 1px', transformOrigin: 'left center', transform: 'rotate(240deg) translate(6px, 0px)', borderRadius: 1}} /></div>
                                <div style={{position: 'absolute', top: '-1px', opacity: '0.25', animation: '1s linear 0s infinite normal none running opacity-100-25-9-12'}}>
                                    <div style={{position: 'absolute', width: '5.6px', height: 2, background: 'rgb(221, 221, 221)', boxShadow: 'rgba(0, 0, 0, 0.1) 0px 0px 1px', transformOrigin: 'left center', transform: 'rotate(270deg) translate(6px, 0px)', borderRadius: 1}} /></div><div style={{position: 'absolute', top: '-1px', opacity: '0.25', animation: '1s linear 0s infinite normal none running opacity-100-25-10-12'}}><div style={{position: 'absolute', width: '5.6px', height: 2, background: 'rgb(221, 221, 221)', boxShadow: 'rgba(0, 0, 0, 0.1) 0px 0px 1px', transformOrigin: 'left center', transform: 'rotate(300deg) translate(6px, 0px)', borderRadius: 1}} /></div><div style={{position: 'absolute', top: '-1px', opacity: '0.25', animation: '1s linear 0s infinite normal none running opacity-100-25-11-12'}}><div style={{position: 'absolute', width: '5.6px', height: 2, background: 'rgb(221, 221, 221)', boxShadow: 'rgba(0, 0, 0, 0.1) 0px 0px 1px', transformOrigin: 'left center', transform: 'rotate(330deg) translate(6px, 0px)', borderRadius: 1}} />
                    </div>
                </div>
        </span>

    );
}

export default Spinner;
