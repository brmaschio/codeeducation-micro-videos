import * as React from 'react';
import { useEffect, useState } from "react";

import MUIDataTable, { MUIDataTableColumn } from 'mui-datatables';

import format from "date-fns/format";
import parseISO from "date-fns/parseISO";

import castMemberHttp from "../../util/http/cast-member-http";

export const CastMembersTypes = {
    1: 'Diretor',
    2: 'Ator'
}

const columnsDefinitions: MUIDataTableColumn[] = [
    {
        name: "name",
        label: "Nome",
    },
    {
        name: "type",
        label: "Tipo",
        options: {
            customBodyRender(value, tableMeta, updateValue) {
                return CastMembersTypes[value];
            }
        },
    },
    {
        name: "created_at",
        label: "Criado em",
        options: {
            customBodyRender(value, tableMeta, updateValue) {
                return <span> {format(parseISO(value), 'dd/MM/yyyy')}</span>
            }
        },
    }
];

const Table = () => {

    const [data, setData] = useState([]);

    useEffect(() => {

        castMemberHttp.list().then(response => {
            setData(response.data.data);
        });

    }, []);

    return (
        <MUIDataTable data={data} title="Categorias" columns={columnsDefinitions} ></MUIDataTable>
    )

}


export default Table;