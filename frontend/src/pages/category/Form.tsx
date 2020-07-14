import React, { useEffect, useState } from 'react';
import { useParams, useHistory } from 'react-router-dom';

import { useForm } from "react-hook-form";

import { ButtonProps, TextField, Checkbox, Box, Button, makeStyles, Theme, FormControlLabel } from '@material-ui/core';
import categoryHttp from '../../util/http/category-http';

import { useSnackbar } from "notistack";

import * as yup from '../../util/vendor/yup';

const useStyles = makeStyles((theme: Theme) => {
    return {
        submit: {
            margin: theme.spacing(1)
        }
    }
});

const validationSchema = yup.object().shape({
    name: yup.string().label('nome').required().max(255)
});

const Form = () => {

    const classes = useStyles();

    const { enqueueSnackbar }  = useSnackbar();
    const { id } = useParams();
    const history = useHistory();
    const [category, setCategory] = useState<{ id: string } | null>(null);
    const [loading, setLoading] = useState(false);

    const buttonProps: ButtonProps = {
        className: classes.submit,
        variant: 'contained',
        color: 'secondary',
        disabled: loading
    }

    const { register, handleSubmit, getValues, errors, reset, watch, setValue } = useForm({
        
        // AGUARDANDO AJUDA DO FORUM
        // validationSchema,
        
        defaultValues: {
            is_active: true
        }
    });

    function onSubmit(formData, event) {

        setLoading(true);

        const http = !category
            ? categoryHttp.create(formData)
            : categoryHttp.update(category.id, formData);

        http.then(({ data }) => {

            enqueueSnackbar('Salvo com sucesso!', { variant: "success" });

            setTimeout(() => {

                console.log(data.data);

                event ? (id
                    ? history.replace(`/categories/${data.data.id}/edit`)
                    : history.push(`/categories/${data.data.id}/edit`)
                )
                    : history.push('/categories');

            })
        }).catch(error => { 
            console.log(error)
            enqueueSnackbar('Erro Ao Processar Serviço Remoto', {variant: "error"});
        }).finally(() => setLoading(false));

    }

    useEffect(() => {

        if (!id) {
            return;
        }

        setLoading(true);

        categoryHttp.get(id).then(({ data }) => {
            setCategory(data.data);
            reset(data.data);
        }).catch(error => { 
            console.log(error)
            enqueueSnackbar('Erro Ao Processar Serviço Remoto', {variant: "error"});
        }).finally(() => setLoading(false));

    }, [id, reset, enqueueSnackbar]);

    useEffect(() => {
        register({ name: 'is_active' });
    }, [register]);

    return (
        <form onSubmit={handleSubmit(onSubmit)}>

            <TextField
                inputRef={register}
                name="name"
                label="Nome"
                fullWidth
                variant={"outlined"}
                error={(errors as any).name !== undefined}
                helperText={(errors as any).name && (errors as any).name.message}
                InputLabelProps={{ shrink: true }}
                disabled={loading}
            />

            <TextField
                inputRef={register}
                name="description"
                label="Descrição"
                multiline
                rows="4"
                fullWidth
                variant={"outlined"}
                margin={"normal"}
                InputLabelProps={{ shrink: true }}
                disabled={loading}
            />

            <FormControlLabel control={
                <Checkbox
                    name={"is_active"}
                    checked={watch('is_active')}
                    onChange={() => setValue('is_active', !getValues()['is_active'])}
                />
            }
                label="Ativo?"
                labelPlacement={'end'}
                disabled={loading}
            />

            <Box dir={"rtl"}>
                <Button onClick={() => onSubmit(getValues(), null)} {...buttonProps} >Salvar</Button>
                <Button type="submit" {...buttonProps} >Salvar e Continuar</Button>
            </Box>

        </form >
    );
};

export default Form;