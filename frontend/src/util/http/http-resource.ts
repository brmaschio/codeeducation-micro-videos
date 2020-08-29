import { AxiosInstance, AxiosResponse, AxiosRequestConfig, CancelTokenSource } from "axios";
import Axios from 'axios';
import { objectToFormData } from 'object-to-formdata';

export default class HttpResource {

    private cancelList: CancelTokenSource | null = null;

    constructor(protected http: AxiosInstance, protected resource) {
    }

    list<T = any>(options?: { queryParams?}): Promise<AxiosResponse<T>> {

        if (this.cancelList) {
            this.cancelList.cancel("List request cancelled");
        }
        this.cancelList = Axios.CancelToken.source();

        const config: AxiosRequestConfig = {
            cancelToken: this.cancelList.token,
        };

        if (options && options.queryParams) {
            config.params = options.queryParams;
        }

        return this.http.get<T>(this.resource, config);
    }

    get<T = any>(id: any): Promise<AxiosResponse<T>> {
        return this.http.get<T>(`${this.resource}/${id}`);
    }

    create<T = any>(data): Promise<AxiosResponse<T>> {
        return this.http.post<T>(this.resource, data);
    }

    update<T = any>(id: any, data, options?: { http?: { usePost: boolean } }): Promise<AxiosResponse<T>> {
        let sendData = data;
        if (this.containsFile(data)) {
            sendData = this.getFormData(data);
        }
        const { http } = (options || {}) as any;
        return !options || !http || !http.usePost
            ? this.http.put<T>(`${this.resource}/${id}`, sendData)
            : this.http.post<T>(`${this.resource}/${id}`, sendData)
    }

    makeSendData(data) {
        return this.containsFile(data) ? this.getFormData(data) : data;
    }

    containsFile(data) {
        return Object.values(data).filter(el => el instanceof File).length !== 0;
    }

    getFormData(data) {
        return objectToFormData(data, { booleansAsIntegers: true });
    }

    delete<T = any>(id: any): Promise<AxiosResponse<T>> {
        return this.http.delete<T>(`${this.resource}/${id}`);
    }

    isCancelledRequest(error) {
        return Axios.isCancel(error);
    }

    deleteCollection<T = any>(queryParams): Promise<AxiosResponse<T>> {
        const config: AxiosRequestConfig = {};

        if (queryParams) {
            config['params'] = queryParams;
        }
        return this.http.delete<T>(`${this.resource}`, config);
    }

}