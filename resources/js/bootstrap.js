import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

import { Ziggy } from './ziggy.js';
import { route as ziggyRoute } from 'ziggy-js';

window.route = (name, params, absolute, config = Ziggy) => ziggyRoute(name, params, absolute, config);