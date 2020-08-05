import * as React from 'react';
import { useEffect, useState } from "react";
import { useParams } from 'react-router';
import { useHistory } from 'react-router-dom';

import { useForm } from 'react-hook-form';
import { useSnackbar } from "notistack";

import { Checkbox, TextField } from "@material-ui/core";
import FormControlLabel from "@material-ui/core/FormControlLabel";

import categoryHttp from "../../util/http/category-http";
import * as yup from '../../util/vendor/yup';
import { Category, simpleResponse } from "../../util/models";
import SubmitActions from "../../components/SubmitActions";
import { DefaultForm } from "../../components/DefaultForm";

const validationSchema = yup.object().shape({
    name: yup.string().label('nome').required().max(255)
});

export default function Form() {

    const {
        register,
        handleSubmit,
        getValues,
        errors,
        reset,
        watch,
        setValue,
        triggerValidation
    } = useForm({
        defaultValues: {
            is_active: true
        },
        validationSchema
    });

    const { enqueueSnackbar } = useSnackbar();
    const history = useHistory();
    const { id } = useParams();
    const [category, setCategory] = useState<Category | null>(null);
    const [loading, setLoading] = useState<boolean>(false);


    useEffect(() => {
        register({ name: 'is_active' });
    }, [register]);



    useEffect(() => {

        if (!id) {
            return;
        }

        (async function getCategory() {
            setLoading(true);
            try {
                const { data } = await categoryHttp.get(id);
                setCategory(data.data);
                reset(data.data);
            } catch (e) {
                console.log(e);
                enqueueSnackbar("Não foi possível carregar as informações", { variant: "error" });
            } finally {
                setLoading(false)
            }
        })();


    }, [id, reset, enqueueSnackbar]);

    async function onSubmit(formData, event) {
        setLoading(true);
        try {
            const http = !category
                ? categoryHttp.create<simpleResponse<Category>>(formData)
                : categoryHttp.update<simpleResponse<Category>>(category.id, formData);

            const { data } = await http;
            enqueueSnackbar('Categoria salva com sucesso!', { variant: "success" });
            setLoading(false);
            event
                ? (
                    id
                        ? history.replace(`/categories/${data.data.id}/edit`)
                        : history.push(`/categories/${data.data.id}/edit`)
                )
                : history.push('/categories');
        } catch (e) {
            console.log(e);
            enqueueSnackbar('Não foi possível salvar a categoria!', { variant: "error" });
            setLoading(false)
        }
    }

    function validateSubmit() {
        triggerValidation()
            .then(isValid => { isValid && onSubmit(getValues(), null) });
    }

    return (

        <DefaultForm GridItemProps={{ xs: 12, md: 6 }} onSubmit={handleSubmit(onSubmit)}>
            <TextField
                name="name"
                label="Nome"
                fullWidth
                variant={"outlined"}
                inputRef={register}
                disabled={loading}
                error={(errors as any).name !== undefined}
                helperText={(errors as any).name && (errors as any).name.message}
                InputLabelProps={{ shrink: true }}
            />
            <TextField
                name="description"
                label="Descrição"
                multiline
                rows="4"
                fullWidth
                variant={"outlined"}
                margin="normal"
                inputRef={register}
                disabled={loading}
                InputLabelProps={{ shrink: true }}
            />

            <FormControlLabel
                control=
                {<Checkbox
                    name="is_active"
                    color={"primary"}
                    onChange={() => setValue('is_active', !getValues()['is_active'])}
                    checked={(watch('is_active') as boolean)}
                />
                }
                label={"Ativo?"}
                labelPlacement={"end"}
                disabled={loading as boolean} />

            <SubmitActions disabledButtons={loading} handleSave={validateSubmit} />
        </DefaultForm>
    );
};