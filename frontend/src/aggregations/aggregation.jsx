import React, { memo, lazy, Suspense } from "react";

import Average from "./average.jsx";
/**
 * Terms est un composant lourd, donc on le charge en lazy
 */
const Terms = lazy(() => import("./terms.jsx"));

const Loading = <div>⚙️</div>;

/**
 * Composant générique pour représenter les différents types d'aggrégations
 */
function Aggregation(props) {
    const { question, aggregationType, aggregation } = props;
    let visualization = <p>Pas de visualisation pour cette aggrégation</p>;
    if (aggregationType === "terms") {
        visualization = (
            <Suspense fallback={Loading}>
                <Terms {...props} />
            </Suspense>
        );
    } else if (aggregationType === "average") {
        visualization = <Average {...props} />;
    }
    return (
        <div className="aggregation" data-cy="aggregation-container">
            <h4>{question}</h4>
            <div className="visualization">{visualization}</div>
        </div>
    );
}

export default memo(Aggregation);
