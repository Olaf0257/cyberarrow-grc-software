import React, { Fragment, useEffect, useState } from 'react';
import TreeSelect, { SHOW_PARENT } from "rc-tree-select";
import "rc-tree-select/assets/index.less";

function CustomTreeSelect(props) {
    const [defaultValue, setDefaultValue] = useState(undefined);
    // const [treeData, setTreeData] = useState([]);

    const treeData = [
        {
            "value": "1-0",
            "label": "Organization 1",
            "children": [
                {
                    "value": "1-1",
                    "label": "Amrit",
                    "children": [
                        {
                            "value": "1-2",
                            "label": "pp"
                        }
                    ]
                },
                {
                    "value": "1-3",
                    "label": "PPP"
                },
                {
                    "value": "1-4",
                    "label": "IT DEPARTMENT"
                },
                {
                    "value": "1-5",
                    "label": "affsda"
                }
            ]
        }
    ];

    // useEffect(async () => {

    //     /* Requesting data scope tree dropdown data*/
    //     let httpResponse = await axiosFetch.get('/data-scope/get-tree-view-data')
    //     let response = httpResponse.data

    //     if (response.success) {
    //         let data = response.data
    //         setTreeData(data)
    //     }
    // }, [])

    useEffect(() => {
        let defaultDataScope = treeData[0]['value']
        setDefaultValue(defaultDataScope)
    }, [treeData])

    return (
        <Fragment>
            {defaultValue && <TreeSelect
                defaultValue={defaultValue}
                dropdownStyle={{ zIndex: '1002', position: 'fixed' }}
                dropdownMatchSelectWidth
                treeLine="true"
                treeDefaultExpandAll
                treeIcon=""
                style={{ width: 1000 }}
                treeData={treeData}
            />
            }
        </Fragment>
    );
}

export default CustomTreeSelect;
