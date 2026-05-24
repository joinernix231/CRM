const FIELD_LABELS = {
    name: 'Nombre',
    email: 'Email',
    phone: 'Teléfono',
    company: 'Empresa',
    status: 'Estado',
    position: 'Cargo',
    is_primary: 'Contacto principal',
    client_id: 'Cliente',
};

function isValidationErrorBag(value) {
    if (!value || typeof value !== 'object' || Array.isArray(value)) {
        return false;
    }

    const entries = Object.entries(value);

    if (entries.length === 0) {
        return false;
    }

    return entries.every(([, messages]) => {
        if (typeof messages === 'string') {
            return true;
        }

        return Array.isArray(messages) && messages.every((item) => typeof item === 'string');
    });
}

function normalizeErrorBag(bag) {
    return Object.fromEntries(
        Object.entries(bag).map(([field, messages]) => {
            const text = Array.isArray(messages) ? messages[0] : messages;

            return [field, String(text ?? '')];
        })
    );
}

export function parseValidationErrors(body) {
    if (!body || typeof body !== 'object') {
        return {};
    }

    if (isValidationErrorBag(body.errors)) {
        return normalizeErrorBag(body.errors);
    }

    if (isValidationErrorBag(body.data)) {
        return normalizeErrorBag(body.data);
    }

    return {};
}

export function fieldLabel(field) {
    return FIELD_LABELS[field] ?? field;
}

export function parseApiErrorBody(body, fallback = 'Ha ocurrido un error') {
    if (!body || typeof body !== 'object') {
        return {
            message: fallback,
            fieldErrors: {},
        };
    }

    const fieldErrors = parseValidationErrors(body);
    const detail = Object.values(fieldErrors).filter(Boolean).join(' ');

    return {
        message: body.message ?? detail ?? fallback,
        fieldErrors,
    };
}

export function parseHttpError(error, fallback = 'Ha ocurrido un error') {
    const body = error?.response?.data ?? error?.data ?? error;

    return parseApiErrorBody(body, fallback);
}

export function getHttpErrorMessage(error, fallback = 'Ha ocurrido un error') {
    const { message, fieldErrors } = parseHttpError(error, fallback);
    const details = Object.values(fieldErrors).filter(Boolean);

    if (details.length === 0) {
        return message;
    }

    return [message, ...details].join(' ');
}

export function hasValidationErrors(fieldErrors) {
    return fieldErrors && Object.keys(fieldErrors).length > 0;
}

export function attachValidationToError(error, fallback = 'Ha ocurrido un error') {
    const parsed = parseHttpError(error, fallback);

    error.validationMessage = parsed.message;
    error.fieldErrors = parsed.fieldErrors;

    return parsed;
}
