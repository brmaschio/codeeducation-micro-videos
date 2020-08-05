import * as React from 'react';
import { useEffect, useState } from "react";
import { useParams } from 'react-router';
import { useHistory } from 'react-router-dom';

import { useForm } from "react-hook-form";
import { useSnackbar } from "notistack";

import { TextField } from "@material-ui/core";
import MenuItem from "@material-ui/core/MenuItem";

import categoryHttp from "../../util/http/category-http";
import genreHttp from "../../util/http/genre-http";
import { Category, simpleResponse } from "../../util/models";
import { Genre } from "../../util/models";
import * as yup from '../../util/vendor/yup';
import SubmitActions from "../../components/SubmitActions";

const validationSchema = yup.object().shape({
    name: yup.string().label('Nome').required().max(255),
    categories_id: yup.array().label("Categorias").required()
});

export default function Form() {

    const { register, handleSubmit, getValues, setValue, watch, reset, errors, triggerValidation } = useForm({
        defaultValues: {
            categories_id: []
        },
        validationSchema
    });

    const { enqueueSnackbar } = useSnackbar();
    const history = useHistory();
    const { id } = useParams();
    const [categories, setCategories] = useState<Category[]>([]);
    const [genre, setGenre] = useState<Genre | null>(null);
    const [loading, setLoading] = useState(false);

    useEffect(() => {
        register({
            name: 'categories_id'
        });
    }, [register]);

    useEffect(() => {

        let isSubscribed = true;

        (async () => {
            setLoading(true);

            const promises = [categoryHttp.list({ queryParams: { all: '' } })];

            if (id) {
                promises.push(genreHttp.get(id));
            }

            try {
                const [categoriesResponse, genreResponse] = await Promise.all(promises);

                if (isSubscribed) {
                    setCategories(categoriesResponse.data.data);

                    if (id) {
                        setGenre(genreResponse.data.data);
                        const categories_id = genreResponse.data.data.categories.map(category => category.id);
                        reset({
                            ...genreResponse.data.data,
                            categories_id
                        });
                    }
                }

            } catch (e) {
                console.log(e);
                enqueueSnackbar("Não foi possível carregar as informações", { variant: "error" });
            } finally {
                setLoading(false);
            }
        })();


        return () => { isSubscribed = false }

    }, [id, reset, enqueueSnackbar]);


    const handleCategoriesChange = (event: React.ChangeEvent<{ value: unknown }>) => {
        setValue('categories_id', event.target.value as Array<any>);
    };

    async function onSubmit(formData, event) {
        setLoading(true);

        try {
            const http = !genre
                ? genreHttp.create<simpleResponse<Genre>>(formData)
                : genreHttp.update<simpleResponse<Genre>>(genre.id, formData);

            const { data } = await http;
            enqueueSnackbar("Gênero salvo com sucesso!", { variant: "success" });
            setLoading(false);

            event
                ? (
                    id
                        ? history.replace(`/genres/${data.data.id}/edit`)
                        : history.push(`/genres/${data.data.id}/edit`)
                )
                : history.push('/genres');
        } catch (e) {
            console.log(e);
            enqueueSnackbar("Não foi possível salvar o gênero", { variant: "error" });
            setLoading(false);

        }

    }

    function validateSubmit() {
        triggerValidation()
            .then(isValid => { isValid && onSubmit(getValues(), null) });
    }

    return (
        <form onSubmit={handleSubmit(onSubmit)}>
            <TextField
                name={"name"}
                label={"Nome"}
                variant={"outlined"}
                fullWidth
                inputRef={register}
                disabled={loading}
                InputLabelProps={{ shrink: true }}
                error={(errors as any).name !== undefined}
                helperText={(errors as any).name && (errors as any).name.message}

            />

            <TextField
                select
                name={"categories_id"}
                value={watch('categories_id')}
                label={"Categorias"}
                variant={"outlined"}
                margin={"normal"}
                fullWidth
                onChange={handleCategoriesChange}
                SelectProps={{
                    multiple: true
                }}
                InputLabelProps={{ shrink: true }}
                disabled={loading}
                error={(errors as any).categories_id !== undefined}
                helperText={(errors as any).categories_id && (errors as any).categories_id.message}
            >
                <MenuItem value="" disabled>
                    <em>Selecione categorias</em>
                </MenuItem>
                <hr />
                {
                    categories.map(
                        (category, key) => (
                            <MenuItem key={key} value={category.id}>{category.name}</MenuItem>
                        )
                    )
                }

            </TextField>

            <SubmitActions disabledButtons={loading} handleSave={validateSubmit} />
        </form>
    );
};