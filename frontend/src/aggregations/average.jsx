import React from "react";

export default function Average(props) {
    const { aggregation } = props;
    if (typeof aggregation.toLocaleString !== "function") {
        return <p>Résultat invalide</p>;
    }
    return (
        <div data-cy="aggregation-average" className="average">
            Moyenne : {aggregation.toLocaleString()}
        </div>
    );
}
