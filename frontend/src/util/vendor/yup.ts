import { LocaleObject, setLocale } from 'yup'

const ptBr: LocaleObject = {
    mixed: {
        // eslint-disable-next-line
        required: '${path} é requerido',
    },
    string: {
        // eslint-disable-next-line
        max: '${path} precisa ter no máximo ${max} caracteres'
    },
    number: {
        // eslint-disable-next-line
        min: '${path} precisa ser no mínimo ${min}'
    }
};

setLocale(ptBr);

export * from 'yup';