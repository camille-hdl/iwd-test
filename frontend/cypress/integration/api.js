/* eslint-disable */

/**
 * Accès à l'API
 */
describe('API backend', function () {

    const surveyCode = "XX2";
    const questionLabel = "What+best+sellers+are+available+in+your+store%3F";
    const aggregationURI = "XX2/question/What+best+sellers+are+available+in+your+store%3F/aggregation/terms";

    it("Devrait faire retourner un tableau vide pour un survey inconnu", function() {
        cy.request({
            url: Cypress.config().apiUrl + "azert123/questions",
        }).as('testRequest');
        cy.get('@testRequest').should((response) => {
            expect(response.body.questions).to.have.length(0);
        });
    });
    it("Devrait faire retourner des questions pour un survey connu", function() {
        cy.request({
            url: Cypress.config().apiUrl + surveyCode +"/questions",
        }).as('testRequest');
        cy.get('@testRequest').should((response) => {
            expect(response.body.questions).to.not.be.empty;
        });
    });
    it("Devrait faire retourner des aggregations pour des questions", function() {
        cy.request({
            url: Cypress.config().apiUrl + surveyCode +"/question/" + questionLabel + "/aggregations",
        }).as('testRequest');
        cy.get('@testRequest').should((response) => {
            expect(response.body.aggregations).to.be.not.empty;
        });
    });
    it("Devrait faire retourner le resultat d'une aggrégation", function() {
        cy.request({
            url: Cypress.config().apiUrl + aggregationURI,
        }).as('testRequest');
        cy.get('@testRequest').should((response) => {
            expect(response.body.aggregation).to.be.not.empty;
        });
    });
});