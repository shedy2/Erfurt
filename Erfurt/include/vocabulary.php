<?php
/******************************************************************************
* CONSTANTS                                                                   *
******************************************************************************/
// TODO use a vocabulary adapter in order to have a cleaner file and not so much constants

// some namespace definitions
define('EF_XML_NS', 'http://www.w3.org/XML/1998/namespace');
define('EF_XSD_NS', 'http://www.w3.org/2001/XMLSchema#');
define('EF_RDF_NS', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#');
define('EF_RDFS_NS', 'http://www.w3.org/2000/01/rdf-schema#');
define('EF_OWL_NS', 'http://www.w3.org/2002/07/owl#');
	
define('EF_OWL_IMPORTS', EF_OWL_NS.'imports');
define('EF_OWL_ONTOLOGY', EF_OWL_NS.'Ontology');
define('EF_OWL_SAMEAS', EF_OWL_NS.'sameAs');
define('EF_OWL_DIFFERENTFROM', EF_OWL_NS.'differentFrom');
define('EF_OWL_CARDINALITY', EF_OWL_NS.'cardinality');
define('EF_OWL_MINCARDINALITY', EF_OWL_NS.'minCardinality');
define('EF_OWL_MAXCARDINALITY', EF_OWL_NS.'maxCardinality');
define('EF_OWL_HASVALUE', EF_OWL_NS.'hasValue');
define('EF_OWL_SOMEVALUESFROM', EF_OWL_NS.'someValuesFrom');
define('EF_OWL_ALLVALUESFROM', EF_OWL_NS.'allValuesFrom');
define('EF_OWL_INTERSECTIONOF', EF_OWL_NS.'intersectionOf');
define('EF_OWL_EQUIVALENTCLASS', EF_OWL_NS.'equivalentClass');
define('EF_OWL_DISJOINTWITH', EF_OWL_NS.'disjointWith');
define('EF_OWL_CLASS', EF_OWL_NS.'Class');
define('EF_OWL_DEPRECATED_CLASS', EF_OWL_NS.'DeprecatedClass');
define('EF_OWL_ANNOTATION_PROPERTY', EF_OWL_NS.'AnnotationProperty');
define('EF_OWL_DATATYPE_PROPERTY', EF_OWL_NS.'DatatypeProperty');
define('EF_OWL_OBJECT_PROPERTY', EF_OWL_NS.'ObjectProperty');
define('EF_OWL_FUNCTIONAL_PROPERTY', EF_OWL_NS.'FunctionalProperty');
define('EF_OWL_INVERSEFUNCTIONAL_PROPERTY', EF_OWL_NS.'InverseFunctionalProperty');
define('EF_OWL_SYMMETRIC_PROPERTY', EF_OWL_NS.'SymmetricProperty');
define('EF_OWL_TRANSITIVE_PROPERTY', EF_OWL_NS.'TransitiveProperty');
define('EF_OWL_DEPRECATED_PROPERTY', EF_OWL_NS.'DeprecatedProperty');
define('EF_OWL_RESTRICTION', EF_OWL_NS.'Restriction');
define('EF_OWL_ONEOF', EF_OWL_NS.'oneOf');
define('EF_OWL_THING', EF_OWL_NS.'Thing');
define('EF_OWL_ONPROPERTY', EF_OWL_NS.'onProperty');
define('EF_OWL_ALLDIFFERENT', EF_OWL_NS.'AllDifferent');
define('EF_OWL_UNIONOF', EF_OWL_NS.'unionOf');
define('EF_OWL_COMPLEMENTOF', EF_OWL_NS.'complementOf');
	
define('EF_RDF_TYPE', EF_RDF_NS.'type');
define('EF_RDF_DESCRIPTION', EF_RDF_NS.'Description');
define('EF_RDF_ABOUT', EF_RDF_NS.'about');
define('EF_RDF_NIL', EF_RDF_NS.'nil');
define('EF_RDF_FIRST', EF_RDF_NS.'first');
define('EF_RDF_REST', EF_RDF_NS.'rest');
define('EF_RDF_PROPERTY', EF_RDF_NS.'Property');
	
define('EF_RDFS_COMMENT', EF_RDFS_NS.'comment');
define('EF_RDFS_LABEL', EF_RDFS_NS.'label');
define('EF_RDFS_SUBCLASSOF', EF_RDFS_NS.'subClassOf');
define('EF_RDFS_SUBPROPERTYOF', EF_RDFS_NS.'subPropertyOf');
define('EF_RDFS_DATATYPE', EF_RDFS_NS.'Datatype');
define('EF_RDFS_CLASS', EF_RDFS_NS.'Class');
define('EF_RDFS_DOMAIN', EF_RDFS_NS.'domain');
define('EF_RDFS_RANGE', EF_RDFS_NS.'range');
	
define('EF_XSD_STRING', EF_XSD_NS.'string');
define('EF_XSD_INTEGER', EF_XSD_NS.'integer');
define('EF_XSD_DECIMAL', EF_XSD_NS.'decimal');
define('EF_XSD_DOUBLE', EF_XSD_NS.'double');
define('EF_XSD_BOOLEAN', EF_XSD_NS.'boolean');
	
define('EF_BNODE_PREFIX', 'node');

define('EF_SERIALIZER_AD', 'Generated by Erfurt Semantic Web Framework - http://sourceforge.net/projects/powl');
