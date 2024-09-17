const localeOptions = ({
    minimumFractionDigits: 2,
    maximumFractionDigits: 3,
    useGrouping: true,
})

const currencies = {
    'RUB': '₽', // Российский рубль
    'USD': '$', // Доллар США
    'EUR': '€', // Евро
    'GBP': '£', // Фунт стерлингов
    'JPY': '¥', // Японская иена
    'CNY': '¥', // Китайский юань
    'INR': '₹', // Индийская рупия
    'AUD': '$', // Австралийский доллар
    'CAD': '$', // Канадский доллар
    'CHF': '₣', // Швейцарский франк
    'NZD': '$', // Новозеландский доллар
    'KRW': '₩', // Южнокорейская вона
};
Number.prototype.toLocaleStringIbt = function (options) {
    options = options ? options : {};
    const formatted = this.toLocaleString("ru-RU", { ...localeOptions, ...options });
    return formatted.replaceAll(",", ".");
}

export function toLocaleStringIbt(string, options) {
    options = options || {};
    const formatted = string.toLocaleString("ru-RU", { ...localeOptions, ...options });
    return formatted.replaceAll(",", ".");
}

export function toLocaleStringIbt_short(string, options) {
    options = options || {};
    const formatted = string.toLocaleString("ru-RU", {
        ...{
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
            useGrouping: true,
        }, ...options });
    return formatted;
}

String.prototype.asCurrency = function () {
    if (currencies[this]) return currencies[this];
    else return this;
}

export function asCurrency(string){
    if (currencies[string]) return currencies[string];
    else return string;
}