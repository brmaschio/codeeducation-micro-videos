import React, { useState, useEffect } from 'react';
import { useForm } from "react-hook-form";
import { ButtonProps, TextField, Checkbox, Box, Button, makeStyles, Theme, MenuItem, FormControlLabel } from '@material-ui/core';
import genreHttp from '../../util/http/genre-http';
import categoryHttp from '../../util/http/category-http';
import { useParams, useHistory } from 'react-router-dom';
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
    name: yup.string().label('Nome').required().max(255),
    categories_id: yup.array().label("Categorias").required()
});

const Form = () => {

    const classes = useStyles();

    const { enqueueSnackbar }  = useSnackbar();
    const { id } = useParams();
    const history = useHistory();
    const [genre, setGenre] = useState<{ id: string } | null>(null);
    const [loading, setLoading] = useState(false);

    const buttonProps: ButtonProps = {
        className: classes.submit,
        variant: 'contained',
        color: 'secondary'
    }

    const [categories, setCategories] = useState([]);

    const { register, handleSubmit, getValues, watch, setValue, reset, errors } = useForm({
        
        // AGUARDANDO AJUDA DO FORUM
        // validationSchema,
        
        defaultValues: {
            is_active: true,
            categories_id: []
        }
    });

    function onSubmit(formData, event) {

        setLoading(true);

        const http = !genre
            ? genreHttp.create(formData)
            : genreHttp.update(genre.id, formData);

        http.then(({ data }) => {

            enqueueSnackbar('Salvo com sucesso!', { variant: "success" });

            setTimeout(() => {

                console.log(data.data);

                event ? (id
                    ? history.replace(`/genres/${data.data.id}/edit`)
                    : history.push(`/genres/${data.data.id}/edit`)
                )
                    : history.push('/genres');

            })
        }).catch(error => { 
            console.log(error)
            enqueueSnackbar('Erro Ao Processar Serviço Remoto', {variant: "error"});
        }).finally(() => setLoading(false));

    }

    useEffect(() => {

        categoryHttp.list().then(response => {
            setCategories(response.data.data);
        }).catch(error => { 
            console.log(error)
            enqueueSnackbar('Erro Ao Processar Serviço Remoto', {variant: "error"});
        });

    }, [enqueueSnackbar]);

    useEffect(() => {

        register({ name: 'categories_id' });
        register({ name: 'is_active' });

    }, [register])

    useEffect(() => {

        if (!id) {
            return;
        }

        setLoading(true);

        genreHttp.get(id).then(({ data }) => {
            console.log(data.data);

            setGenre(data.data);
            const categories_id = data.data.categories.map(category => category.id);
            reset({ ...data.data, categories_id })

        }).catch(error => { 
            console.log(error)
            enqueueSnackbar('Erro Ao Processar Serviço Remoto', {variant: "error"});
        }).finally(() => setLoading(false));

    }, [id, reset, enqueueSnackbar]);

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
                select
                name="categories_id"
                value={watch('categories_id')}
                label={"Categorias"}
                variant={"outlined"}
                margin={"normal"}
                fullWidth
                onChange={event => setValue(event.target.name, event.target.value)}
                SelectProps={{ multiple: true }}
                InputLabelProps={{ shrink: true }}
                disabled={loading}
                error={(errors as any).categories_id !== undefined}
                helperText={(errors as any).categories_id && (errors as any).categories_id.message}
            >
                <MenuItem value="" disabled>
                    <em>Categorias</em>
                </MenuItem>

                {categories.map((value: any, key) => (
                    <MenuItem key={key} value={value.id}>{value.name}</MenuItem>
                ))}

            </TextField>


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

        </form>
    );
};

export default Form;