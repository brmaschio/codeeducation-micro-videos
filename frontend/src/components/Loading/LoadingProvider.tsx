import * as React from 'react';
import { useEffect, useMemo, useState } from "react";

import LoadingContext from "./LoadingContext";
import {
    addGlobalRequestInterceptor,
    addGlobalResponseInterceptor,
    removeGlobalRequestInterceptor,
    removeGlobalResponseInterceptor
} from "../../util/http";

import { omit } from 'lodash';

export const LoadingProvider = (props) => {

    const [loading, setLoading] = useState(false);
    const [countRequest, setCountRequest] = useState(0);

    useMemo(() => {
        let isSubscribed = true;

        const requestsIds = addGlobalRequestInterceptor(
            config => {
                if (isSubscribed && !config.headers.hasOwnProperty('ignoreLoading')) {
                    setLoading(true);
                    setCountRequest((prevState => prevState + 1))
                }
                config.headers = omit(config.headers, 'ignoreLoading');
                return config
            });

        const responseIds = addGlobalResponseInterceptor(
            response => {
                if (isSubscribed) {
                    decrementCountRequest();
                }
                return response;
            }, error => {
                if (isSubscribed) {
                    decrementCountRequest();
                }
                return Promise.reject(error);
            });


        return () => {
            isSubscribed = false;
            removeGlobalRequestInterceptor(requestsIds);
            removeGlobalResponseInterceptor(responseIds);
        }
    }, []);

    useEffect(() => {
        if (!countRequest) {
            setLoading(false);
        }
    }, [countRequest]);

    function decrementCountRequest() {
        setCountRequest((prevState => prevState - 1))
    }


    return (
        <LoadingContext.Provider value={loading}>
            {props.children}
        </LoadingContext.Provider>
    );
};
