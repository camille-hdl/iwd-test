import React, { useEffect } from "react";
import { useFetcher } from "react-ufo";
import { surveyFetcher, questionsAggregationsFetcher, aggregationResultsFetcher } from "./backend.js";

/**
 * Hook pour récupérer toutes les aggrégations disponibles sur un survey.
 * `[aggregations, loading, error] = useAggregations(surveyCode)`
 *
 * Plusieurs requêtes successives selon l'API disponible en backend.
 * Survey -> [questions] -> [[aggregations]].
 * ⚠️ nombre de requêtes sur le backend ~= nbQuestions * nbAggregations
 */
export default function useAggregations(surveyCode) {
    const [fetchSurvey, [loadingSurvey, surveyError]] = useFetcher(surveyFetcher, { loading: true });
    const [fetchQuestions, [loadingQuestions, questionsError]] = useFetcher(questionsAggregationsFetcher, {
        loading: true,
    });
    const [
        fetchAggregations,
        [loadingAggregations, aggregationsError, aggregations],
    ] = useFetcher(aggregationResultsFetcher, { loading: true });

    useEffect(() => {
        fetchSurvey(surveyCode).then(survey => {
            /**
             * Convertir `survey.questions` en array
             */
            const questions = [];
            for (const label in survey.questions) {
                questions.push({ label: label, ...survey.questions[label] });
            }
            fetchQuestions(questions.map(q => q.aggregationsURI)).then(questions => {
                const aggregations = questions.flatMap(q => q.aggregations);
                fetchAggregations(aggregations);
            });
        });
    }, [surveyCode, fetchSurvey, fetchQuestions, fetchAggregations]);
    return [aggregations, loadingAggregations, surveyError || questionsError || aggregationsError];
}
