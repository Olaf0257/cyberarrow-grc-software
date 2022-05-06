import React, {Fragment, useEffect, useRef, useState} from 'react';
import Timeline from "react-vis-timeline";



function CampaignTimeline(props) {
    const {timelineItems} = props
    const timelineElRef = useRef(null)
    const [showResetZoomBtn, setShowResetZoomBtn] = useState(false)
    const options = {
        stack: false,
        maxMinorChars: 20,
        height: 280,
        tooltip: {
            delay: 1
        }
    };

    useEffect(() => {
        /* Setting the groups */
        timelineElRef.current.timeline.setGroups([
            {
              id: 1,
              content: ""
            }
          ])

        let updatedTimelineItems = timelineItems.map((item) => {
            let parsedItem = JSON.parse(item);
            parsedItem['group'] = 1

            return parsedItem
        })

        /* Setting the timeline items */
        timelineElRef.current.timeline.setItems(updatedTimelineItems)
    }, [timelineItems])

    /* show zoom reset botton on zoom in and out */
    const rangeChangeHandler = (event) => {
        setShowResetZoomBtn(true)
    }

    /* Resetting the zoom */
    const resetTimelineZoom = () => {
        timelineElRef.current.timeline.fit();
    }

    return (
        <Fragment>

            <div className="row my-5">
                <div className="col-12">
                <h4 className="text-center mb-4">Campaign Timeline</h4>
                <div id="campaign-timeline">
                    <Timeline options={options} rangechangeHandler={rangeChangeHandler} ref={timelineElRef}/>
                    {/* Timline zoom reset button */}
                    <button id="campaign-timeline__reset-zoom" onClick={resetTimelineZoom} className={`btn btn-primary theme-bg-secondary ${showResetZoomBtn ? '' : 'd-none'}`} >Reset zoom</button>
                </div>
                </div>
            </div>
        </Fragment>
    );
}

export default CampaignTimeline;
