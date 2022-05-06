import React, {
    forwardRef,
    Fragment,
    useEffect,
    useImperativeHandle,
    useState,
} from "react";
import Modal from "react-bootstrap/Modal";
import DataTable from "../../../../common/data-table/DataTable";

function AddExistingUserModal(props, ref) {
    const [addExistingUserModalShow, setAddExistingUserModalShow] =
        useState(false);
    const [refreshDataTable, setRefreshDataTable] = useState(false);
    const [error, setError] = useState("");
    const title = "Add Existing User To Group";

    const [groupUsers, setGroupUsers] = useState(props.groupUsers);
    const fetchURL = route("policy-management.users-and-groups.users.get-data");

    useImperativeHandle(ref, () => ({
        addExistingUser() {
            setAddExistingUserModalShow(true);
        },
        checkSelectedUsers() {
            //For User Edit

            let dataRow = [];

            for (let user of props.groupUsers.groupsData) {
                dataRow[1] = user.user_first_name
                dataRow[2] = user.user_last_name
                dataRow[3] = user.user_email
                handleCheckboxChange(dataRow);
            }
        },
        clearAllStates() {
            setCheckedState([]);
            setGroupUsers({ groupsData: [] });
        },
    }));

    const [checkedState, setCheckedState] = useState([]);

    function handleCheckboxChange(rowData) {

        var currentData = groupUsers.groupsData;
        var data = {
            user_first_name: rowData[1],
            user_last_name: rowData[2],
            user_email: rowData[3],
        };

        //Checkbox State Management
        let checked = checkedState.filter((item) => item.email == rowData[3]) //checking if checkbox is checked

        if (checked[0]) {
            if (checked[0].checked == "checked") {
                //checkbox is checked so need to uncheck
                checked[0].checked = null;
                const indexToRemove = currentData.indexOf(currentData.filter((item) => item.user_email == rowData[3])[0])
                if (indexToRemove > -1) {
                    currentData.splice(indexToRemove, 1)
                }
            }
            else {
                //check unchecck full cycle complete
                checked[0].checked = "checked";
                currentData.push(data); // Data State
            }
        } else {
            //this is where you first enter after you open the modal
            //checkbox is unchecked so need to check
            var currentState = {
                email: rowData[3],
                checked: 'checked'
            }
            checkedState.push(currentState) // Checkbox State
            currentData.push(data); // Data State
        }
        // setRefreshDataTable(prevState => (!prevState));


        var finalData = {
            groupsData: currentData,
        };
        setGroupUsers(finalData);
    }

    function addToGroup() {
        setAddExistingUserModalShow(false);
        props.actionFunction(groupUsers);
    }

    function computeCheckedOrNot(row) {
        if (row[3]) {
            let checked = checkedState.filter((item) => item.email == row[3])
            if (checked[0]) {
                if (checked[0].checked == "checked") {
                    return "checked";
                }
            } else {
                return null;
            }
        }
    }

    function onModalClose() {
        setError('')
        setAddExistingUserModalShow(false)
    }

    const addUserColumns = [
        {
            accessor: "0",
            label: "Select",
            priorityLevel: 1,
            position: 1,
            minWidth: 150,
            sortable: false,
            CustomComponent: ({ row }) => {
                return (
                    <Fragment>
                        <div className="checkbox checkbox-success">
                            <input
                                id={"user-checkbox" + row[0]}
                                type="checkbox"
                                checked={computeCheckedOrNot(row)}
                                onChange={() => handleCheckboxChange(row)}
                                className='user-checkbox-input'
                            />
                            <label
                                className="user-checkbox-label"
                                htmlFor={"user-checkbox" + row[0]}
                            ></label>
                        </div>
                    </Fragment>
                );
            },
        },
        {
            accessor: "1",
            label: "First Name",
            priorityLevel: 1,
            position: 1,
            minWidth: 150,
        },
        {
            accessor: "2",
            label: "Last Name",
            priorityLevel: 1,
            position: 2,
            minWidth: 150,
        },
        {
            accessor: "3",
            label: "Email",
            priorityLevel: 1,
            position: 3,
            minWidth: 150,
        },
    ];

    return (
        <div>
            {error ? (
                <span className="invalid-feedback d-block">{error}</span>
            ) : (
                ""
            )}
            <Modal
                show={addExistingUserModalShow}
                onHide={onModalClose}
                dialogClassName="modal-90w"
                aria-labelledby="example-custom-modal-styling-title"
                size="lg"
            >
                <Modal.Header className="px-3 pt-3 pb-0" closeButton>
                    <Modal.Title
                        id="example-custom-modal-styling-title"
                        className="my-0"
                    >
                        {title}
                    </Modal.Title>
                </Modal.Header>
                <Modal.Body className="p-3">
                    <div className="modal-body table-container">
                        <DataTable
                            columns={addUserColumns}
                            fetchURL={fetchURL}
                            search={true}
                            refresh={refreshDataTable}
                            refreshOnPageChange
                        />
                    </div>
                    <div className="modal-footer">
                        <button
                            type="button"
                            className="btn btn-secondary waves-effect"
                            data-dismiss="modal"
                            onClick={onModalClose}
                        >
                            Close
                        </button>
                        <button
                            type="button"
                            className="btn btn-primary waves-effect waves-light"
                            id="submit-group-btn"
                            onClick={() => addToGroup()}
                        >
                            Add To Group
                        </button>
                    </div>
                </Modal.Body>
            </Modal>
        </div>
    );
}

export default forwardRef(AddExistingUserModal);
