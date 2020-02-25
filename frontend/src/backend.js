/**
 * ⚠️ Il n'y a aucun cache :
 * * ni cache HTTP géré par le backend
 * * ni serviceworker
 * * ni cache géré "manuellement" par le frontend
 */

const BACKEND = "http://localhost:8080";

/**
 * Retourner l'url complète du backend
 */
const endpoint = uri => [BACKEND, uri].join("");

/**
 * Retourne les questions disponibles sur un survey
 */
export const surveyFetcher = async surveyCode => {
    const url = endpoint("/" + encodeURIComponent(surveyCode) + "/questions");
    const response = await fetch(url);
    return response.json();
};

/**
 * Retourne les aggrégations disponibles sur des questions
 * ⚠️ dans cette fonction comme `aggregationResultFetcher`, on utilise `Promise.all()`,
 * qui risque d'échouer entièrement si le moindre fetch échoue.
 */
export const questionsAggregationsFetcher = async questionAggregationsEndpoints => {
    const urls = questionAggregationsEndpoints.map(endpoint);
    const responses = await Promise.all(urls.map(url => fetch(url)));
    return await Promise.all(responses.map(response => response.json()));
};

/**
 * Retourne le résultat des aggrégations disponibles
 */
export const aggregationResultsFetcher = async aggregationEndpoints => {
    const urls = aggregationEndpoints.map(endpoint);
    const responses = await Promise.all(urls.map(url => fetch(url)));
    return await Promise.all(responses.map(response => response.json()));
};
