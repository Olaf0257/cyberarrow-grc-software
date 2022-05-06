import React, { forwardRef, Fragment, useEffect, useImperativeHandle, useState } from 'react';
import { MultiSelect } from "react-multi-select-component";
import { useDispatch, useSelector } from 'react-redux';
import { fetchProjectFilterData, updateSelectedProjects } from '../../../store/actions/global-dashboard/project-filter';
import { useDidMountEffect } from '../../../custom-hooks';

function ProjectFilterWithoutDataScope(props) {
    const dispatch = useDispatch()
    // const appDataScope = useSelector(state => state.appDataScope.selectedDataScope.value)
    const { selectedDepartment } = useSelector(state => state.commonReducer.departmentFilterReducer)
    const [projects, setProjects] = useState([]);
    // const { projects } = useSelector(state => state.globalDashboardReducer.projectFilterReducer)
    const [selected, setSelected] = useState([]);
    const [projectOptions, setProjectOptions] = useState([])

    // useEffect(() => {
    //     console.log('props:', props);
    //     if (props.selectedProjects)
    //         setSelected(props.selectedProjects)
    // }, [appDataScope])

    useEffect(() => {
        getAllProjects();
    }, [])

    /* Get All Projects */
    const getAllProjects = () => {
        axiosFetch.get(route('common.get-all-projects-without-datascope'),{ params: { compliance_filter: true } }).then(res => {
            let response = res.data
            if (response.success) {
                let data = response.data
                setProjects(data)
            }
        }).catch((error) => { console.log(error) })
    }

    /* Fires when selectedDepartment updates */
    useDidMountEffect(() => {
        // dispatch(fetchProjectFilterData({
        //     data_scope: '1-0',
        //     selected_departments: selectedDepartment.join()
        // }))
        axiosFetch.get(route('common.get-all-projects-without-datascope'), { params: { selected_departments: selectedDepartment.join(), compliance_filter: true} })
            .then(res => {
                let response = res.data
                if (response.success) {
                    let data = response.data
                    setProjects(data)
                }
            }).catch((error) => { console.log(error) })
    }, [selectedDepartment])

    /* Setting project filter options */
    useDidMountEffect(() => {
        /* Un-selecting prev-selected before re-render */
        // unSelectAll()

        let data = projects.map((project) => {
            return {
                value: project.id,
                label: project.name
            }
        })

        setProjectOptions(data)
    }, [projects])

    /* when project options are updated on data scope change */
    useDidMountEffect(() => {
        selectAll()
    }, [projectOptions])


    // The component instance will be extended
    // with whatever you return from the callback passed
    // as the second argument
    // useImperativeHandle(ref, () => ({
    //     selectAll
    // }));

    /* Handling project select*/
    const handleProjectSelect = (selectedProjects) => {
        /*updating local state*/
        setSelected(selectedProjects)
        props.actionFunction(selectedProjects);
    }

    /* Selects the all options */
    const selectAll = () => {
        setSelected(projectOptions)
    }

    /* Un-select all*/
    const unSelectAll = () => {
        setSelected([])
    }

    /* updateSelectedProjects in Store */
    useDidMountEffect(() => {
        dispatch(updateSelectedProjects(selected.map(selectedProject => selectedProject.value)))
    }, [selected])

    return (
        <Fragment>
            <MultiSelect
                options={projectOptions}
                value={selected}
                onChange={handleProjectSelect}
                labelledBy="Select"
                overrideStrings={{
                    allItemsAreSelected: "All projects are selected",
                }}
            />
        </Fragment>
    );
}

export default ProjectFilterWithoutDataScope;
