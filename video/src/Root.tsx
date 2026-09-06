import React from 'react';
import { Composition, Folder } from 'remotion';
import './index.css';

import {
  RomvillCommercial,
  SCENE_DURATIONS,
  TOTAL_DURATION_IN_FRAMES,
} from './RomvillCommercial';
import { Scene1Hook } from './scenes/Scene1Hook';
import { Scene2Identity } from './scenes/Scene2Identity';
import { Scene3Dimensions } from './scenes/Scene3Dimensions';
import { Scene4Advantage } from './scenes/Scene4Advantage';
import { Scene5CallToAction } from './scenes/Scene5CallToAction';

export const RemotionRoot: React.FC = () => {
  return (
    <>
      {/* Master Commercial Video */}
      <Composition
        id="RomvillCommercial"
        component={RomvillCommercial}
        durationInFrames={TOTAL_DURATION_IN_FRAMES}
        fps={30}
        width={1920}
        height={1080}
      />

      {/* Individual Scene Compositions for Isolated Studio Preview */}
      <Folder name="Scenes">
        <Composition
          id="01-Hook"
          component={Scene1Hook}
          durationInFrames={SCENE_DURATIONS.scene1}
          fps={30}
          width={1920}
          height={1080}
        />
        <Composition
          id="02-Identity"
          component={Scene2Identity}
          durationInFrames={SCENE_DURATIONS.scene2}
          fps={30}
          width={1920}
          height={1080}
        />
        <Composition
          id="03-Dimensions"
          component={Scene3Dimensions}
          durationInFrames={SCENE_DURATIONS.scene3}
          fps={30}
          width={1920}
          height={1080}
        />
        <Composition
          id="04-Advantage"
          component={Scene4Advantage}
          durationInFrames={SCENE_DURATIONS.scene4}
          fps={30}
          width={1920}
          height={1080}
        />
        <Composition
          id="05-CallToAction"
          component={Scene5CallToAction}
          durationInFrames={SCENE_DURATIONS.scene5}
          fps={30}
          width={1920}
          height={1080}
        />
      </Folder>
    </>
  );
};
