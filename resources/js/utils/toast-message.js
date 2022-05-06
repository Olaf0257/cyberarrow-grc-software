import {toast} from "react-toastify";

const options = {
    theme: 'colored',
    style: {background: '#51a351', opacity: .8},
    position: toast.POSITION.BOTTOM_RIGHT,
    autoClose: 3000,
    hideProgressBar: false,
    newestOnTop: false,
    rtl: false,
    closeOnClick: true,
    pauseOnFocusLoss: true,
    draggable: true,
    pauseOnHover: true
}

export function showToastMessage (message, type = 'default'){
    switch(type.toLowerCase()){
        case 'info':
            return toast.info(message, options);
        case 'success':
            return toast.success(message, options);
        case 'warning':
            return toast.success(message, options);
        case 'error':
            return toast.error(message, options);
        default:
            return toast(message, options);
    }
}
