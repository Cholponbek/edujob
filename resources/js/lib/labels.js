export const EDUCATION_LEVEL_LABELS = {
    preschool: 'Дошкольное',
    primary: 'Начальное',
    secondary: 'Среднее',
    vocational: 'СПО',
    higher: 'Высшее',
};

export const EMPLOYMENT_TYPE_LABELS = {
    full_time: 'Полная занятость',
    part_time: 'Совместительство',
    either: 'Полная или частичная',
};

export const INSTITUTION_TYPE_LABELS = {
    kindergarten: 'Детский сад',
    school: 'Школа',
    vocational: 'СПО',
    university: 'Вуз',
};

export function educationLevelOptions() {
    return Object.entries(EDUCATION_LEVEL_LABELS).map(([value, label]) => ({ value, label }));
}

export function employmentTypeOptions() {
    return Object.entries(EMPLOYMENT_TYPE_LABELS).map(([value, label]) => ({ value, label }));
}
