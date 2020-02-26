/* eslint-disable */

/**
 * UI
 * Ne fonctionne pas, probablement à cause des appels à un même domaine mais port différent
 * entre front et backend
 */
describe('UI', function () {
    
    before(function () {
        cy.wait(1000);
        cy.visit("/");
    });
    it("Le conteneur devrait être monté", function() {
        cy.get(".App").should("exist");
    });
    it("avoir 2 aggregations", function() {
        cy.get("[data-cy=aggregation-container]").should("have.length", 2);
    });
    it("avoir au moins 1 terms", function() {
        cy.get("[data-cy=aggregation-terms]").should("exist");
    });
    it("avoir au moins 1 average", function() {
        cy.get("[data-cy=aggregation-average]").should("exist");
    });
});