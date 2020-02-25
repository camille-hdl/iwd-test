import React, { useState } from "react";
import useAggregations from "./use-aggregations.js";
import Aggregation from "./aggregations/aggregation.jsx";
import "./App.css";

function App() {
    const [surveyCode, setSurveyCode] = useState("XX2");
    const [aggregations, loading, error] = useAggregations(surveyCode);
    return (
        <div className="App">
            <header className="App-header">
                <h1>IWD Challenge fullstack senior</h1>
                {/* <select onChange={e => {
                  setSurveyCode(e.target.value);
                }} value={surveyCode}>
                  <option value="XX1">XX1</option>
                  <option value="XX2">XX2</option>
                  <option value="XX3">XX3</option>
                </select> */}
                {error ? <h2>😓 Une erreur s'est produite</h2> : loading ? <h2>😴 Chargement...</h2> : null}
                {aggregations ? aggregations.map((aggregation, i) => <Aggregation {...aggregation} key={i} />) : null}
            </header>
        </div>
    );
}

export default App;
