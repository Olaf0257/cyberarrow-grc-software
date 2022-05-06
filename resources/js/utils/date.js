import moment from "moment/moment";

export const transformDate = date => moment(date).format('D MMM, YYYY');
export const transformDateTime = datetime => moment(datetime).format('DD-MM-YYYY hh:mm A')
export const campaignDateTime = datetime => moment(datetime).format('D MMM YYYY, hh:mm A')