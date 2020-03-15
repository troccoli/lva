describe('Authentication', () => {

  it('Register a user', () => {
    cy.visit('/register');

    cy.contains('Register');
    cy.get('#inputName').should('be.visible');
    cy.get('#inputEmail').should('be.visible');
    cy.get('#inputPassword').should('be.visible');
    cy.get('#inputConfirmPassword').should('be.visible');
    cy.get('button[type="submit"]').should('be.visible');
  });

});
