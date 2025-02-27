import { IAutoPlacement } from "../../ts-diagram";
export interface IEditorConfig {
    shapeType?: string;
    type?: string;
    gridStep?: number;
    reservedWidth: number;
    editMode?: boolean;
    lineGap?: number;
    defaults?: any;
    scale?: number;
    autoplacement?: IAutoPlacement;
    controls?: IEditorControls;
}
export interface IEditorControls {
    apply?: boolean;
    reset?: boolean;
    export?: boolean;
    import?: boolean;
    autoLayout?: boolean;
    historyManager?: boolean;
    editManager?: boolean;
    scale?: boolean;
    gridStep?: boolean;
}
export interface ISelectionBox {
    start: ICoords;
    end: ICoords;
}
export interface ISections {
    [key: string]: string[];
}
export interface IFreeEditorConfig extends IEditorConfig {
    shapeBarWidth?: number;
    shapeSections?: ISections;
    gapPreview?: string | number;
    scalePreview?: string | number;
}
export interface ICoords {
    x: number;
    y: number;
}
export interface IDataHash {
    [id: string]: string | number | boolean;
}
export interface IContent {
    shapes?: string;
    width?: number;
    height?: number;
    minX?: number;
    minY?: number;
    gap?: number;
    container?: any;
    root?: string;
    scroll?: any;
}
export interface ISidebarConfig {
    showGridStep?: boolean;
}
export declare enum DiagramEditorEvents {
    resetButton = "resetbutton",
    applyButton = "applybutton",
    undoButton = "undoButton",
    redoButton = "redoButton",
    shapeMove = "shapemove",
    shapeResize = "shaperesize",
    zoomIn = "zoomin",
    zoomOut = "zoomout",
    visibility = "visibility",
    shapesUp = "shapesup",
    exportData = "exportData",
    importData = "importData",
    blockSelectionFinished = "blockSelectionFinished",
    blockSelectionAreaChanged = "blockSelectionAreaChanged",
    autoLayout = "autoLayout",
    changeGridStep = "changeGridStep"
}
export interface IDiagramEditorHandlersMap {
    [key: string]: (...args: any[]) => any;
    [DiagramEditorEvents.resetButton]: () => any;
    [DiagramEditorEvents.applyButton]: () => any;
    [DiagramEditorEvents.undoButton]: () => any;
    [DiagramEditorEvents.redoButton]: () => any;
    [DiagramEditorEvents.shapeMove]: () => any;
    [DiagramEditorEvents.shapeResize]: () => any;
    [DiagramEditorEvents.zoomIn]: () => any;
    [DiagramEditorEvents.zoomOut]: () => any;
    [DiagramEditorEvents.visibility]: () => any;
    [DiagramEditorEvents.exportData]: () => any;
    [DiagramEditorEvents.importData]: (data: any) => any;
    [DiagramEditorEvents.shapesUp]: (shape: any) => any;
    [DiagramEditorEvents.changeGridStep]: (step: number) => any;
}
